<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssistantController extends Controller
{
    // ─── Conversations ────────────────────────────────────────────────────────────

    /**
     * GET /api/v1/assistant/conversations
     * List authenticated user's conversations (paginated, most recent first).
     */
    public function listConversations(Request $request): JsonResponse
    {
        $conversations = Conversation::where('user_id', $request->user()->id)
            ->where('status', '!=', Conversation::STATUS_ARCHIVED)
            ->orderBy('updated_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        // Attach last message preview
        $items = collect($conversations->items())->map(function (Conversation $conv) {
            $lastMessage = Message::where('conversation_id', $conv->id)
                ->orderBy('created_at', 'desc')
                ->first();

            return array_merge($conv->toArray(), [
                'last_message' => $lastMessage ? [
                    'role'       => $lastMessage->role,
                    'content'    => Str::limit($lastMessage->content, 120),
                    'created_at' => $lastMessage->created_at,
                ] : null,
            ]);
        });

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => [
                'current_page' => $conversations->currentPage(),
                'per_page'     => $conversations->perPage(),
                'total'        => $conversations->total(),
                'last_page'    => $conversations->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/v1/assistant/conversations
     * Create a new conversation session.
     */
    public function createConversation(Request $request): JsonResponse
    {
        $request->validate([
            'title'              => ['nullable', 'string', 'max:255'],
            'interview_progress' => ['nullable', 'array'],
        ]);

        $conversation = Conversation::create([
            'user_id'            => $request->user()->id,
            'session_id'         => Str::uuid()->toString(),
            'title'              => $request->input('title', 'New Conversation'),
            'status'             => Conversation::STATUS_ACTIVE,
            'interview_progress' => $request->input('interview_progress', []),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation started.',
            'data'    => $conversation,
        ], 201);
    }

    /**
     * GET /api/v1/assistant/conversations/{id}
     * Get full message thread for a conversation.
     */
    public function showConversation(Request $request, string $id): JsonResponse
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $messages = Message::where('conversation_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => array_merge($conversation->toArray(), [
                'messages' => $messages,
            ]),
        ]);
    }

    /**
     * DELETE /api/v1/assistant/conversations/{id}
     * Archive/close a conversation.
     */
    public function archiveConversation(Request $request, string $id): JsonResponse
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $conversation->update(['status' => Conversation::STATUS_ARCHIVED]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation archived.',
        ]);
    }

    /**
     * PATCH /api/v1/assistant/conversations/{id}/progress
     * Update the interview progress state.
     */
    public function updateProgress(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'interview_progress' => ['required', 'array'],
        ]);

        $conversation = Conversation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $conversation->update([
            'interview_progress' => $request->interview_progress,
            'title'              => $request->input('title', $conversation->title),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Progress updated.',
            'data'    => $conversation->fresh(),
        ]);
    }

    // ─── Messages ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/v1/assistant/conversations/{id}/messages
     *
     * Append one or more messages to a conversation.
     * The frontend sends the finished message(s) after getting AI response.
     *
     * Body:
     * {
     *   "messages": [
     *     { "role": "user", "content": "...", "context_metadata": {} },
     *     { "role": "assistant", "content": "...", "context_metadata": { "scheme_ids": ["..."] } }
     *   ]
     * }
     */
    public function storeMessages(Request $request, string $id): JsonResponse
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'messages'                    => ['required', 'array', 'min:1', 'max:10'],
            'messages.*.role'             => ['required', 'in:user,assistant,system'],
            'messages.*.content'          => ['required', 'string'],
            'messages.*.context_metadata' => ['nullable', 'array'],
        ]);

        $created = [];
        foreach ($request->messages as $msgData) {
            $created[] = Message::create([
                'conversation_id'  => $conversation->id,
                'role'             => $msgData['role'],
                'content'          => $msgData['content'],
                'context_metadata' => $msgData['context_metadata'] ?? null,
            ]);
        }

        // Touch the conversation so it bubbles to top of history
        $conversation->touch();

        return response()->json([
            'success' => true,
            'message' => count($created).' message(s) saved.',
            'data'    => $created,
        ], 201);
    }
}
