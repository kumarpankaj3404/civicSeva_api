<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Scheme;
use Illuminate\Database\Seeder;

class SchemeSeeder extends Seeder
{
    public function run(): void
    {
        $cats = Category::all()->keyBy('slug');

        $schemes = [
            // ─── Agriculture ─────────────────────────────────────────────────────────
            [
                'slug'     => 'agriculture-farming',
                'title'    => 'PM-KISAN (PM Kisan Samman Nidhi)',
                'ministry' => 'Ministry of Agriculture & Farmers Welfare',
                'description' => 'Provides income support of ₹6,000/year to all landholding farmer families, payable in three equal installments of ₹2,000.',
                'benefits' => ['₹6,000 per year direct income support', 'Three installments of ₹2,000', 'Direct Bank Transfer'],
                'eligibility_rules' => [
                    ['field' => 'occupation', 'operator' => 'in',  'value' => ['farmer', 'agricultural_worker'], 'label' => 'Must be a farmer or agricultural worker'],
                    ['field' => 'land_ownership', 'operator' => '==', 'value' => true, 'label' => 'Must own cultivable land'],
                    ['field' => 'annual_income', 'operator' => '<=', 'value' => 200000, 'label' => 'Annual income ≤ ₹2,00,000'],
                ],
                'required_documents' => ['Aadhaar Card', 'Land Records (Khasra/Khatauni)', 'Bank Passbook', 'Mobile Number'],
                'application_url' => 'https://pmkisan.gov.in',
                'tags' => ['farmer', 'income support', 'direct transfer', 'agriculture'],
            ],
            [
                'slug'     => 'agriculture-farming',
                'title'    => 'PM Fasal Bima Yojana (PMFBY)',
                'ministry' => 'Ministry of Agriculture & Farmers Welfare',
                'description' => 'Comprehensive crop insurance scheme providing financial support to farmers suffering crop loss due to natural calamities, pests and diseases.',
                'benefits' => ['Low premium rates (2% Kharif, 1.5% Rabi)', 'Coverage for all food & oilseed crops', 'Quick claim settlement'],
                'eligibility_rules' => [
                    ['field' => 'occupation', 'operator' => 'in', 'value' => ['farmer', 'agricultural_worker'], 'label' => 'Must be a farmer'],
                    ['field' => 'land_ownership', 'operator' => '==', 'value' => true, 'label' => 'Must own or lease cultivable land'],
                ],
                'required_documents' => ['Aadhaar Card', 'Land Documents', 'Bank Account Details', 'Sowing Certificate'],
                'application_url' => 'https://pmfby.gov.in',
                'tags' => ['insurance', 'crop', 'farmer', 'agriculture'],
            ],

            // ─── Education ────────────────────────────────────────────────────────────
            [
                'slug'     => 'education-scholarships',
                'title'    => 'Post Matric Scholarship for SC Students',
                'ministry' => 'Ministry of Social Justice & Empowerment',
                'description' => 'Financial assistance to SC students studying at post-matriculation or post-secondary stage to enable them to complete their education.',
                'benefits' => ['Maintenance allowance', 'Study tour charges', 'Thesis/Dissertation allowance', 'Book allowance'],
                'eligibility_rules' => [
                    ['field' => 'caste_category', 'operator' => '==', 'value' => 'sc', 'label' => 'Must belong to Scheduled Caste'],
                    ['field' => 'annual_income', 'operator' => '<=', 'value' => 250000, 'label' => 'Family annual income ≤ ₹2,50,000'],
                    ['field' => 'education_level', 'operator' => 'in', 'value' => ['11th', '12th', 'graduate', 'post_graduate', 'phd'], 'label' => 'Must be studying post-matric'],
                ],
                'required_documents' => ['Caste Certificate', 'Income Certificate', 'Last Year Marksheet', 'Aadhaar Card', 'Bank Account'],
                'application_url' => 'https://scholarships.gov.in',
                'tags' => ['scholarship', 'sc', 'education', 'post-matric'],
            ],
            [
                'slug'     => 'education-scholarships',
                'title'    => 'Central Sector Scholarship for College Students',
                'ministry' => 'Ministry of Education',
                'description' => 'Merit-based scholarship for students from families with less than ₹8 lakh annual income who scored above 80th percentile in Class XII.',
                'benefits' => ['₹12,000/year for undergraduate', '₹20,000/year for postgraduate', 'Paid for up to 5 years'],
                'eligibility_rules' => [
                    ['field' => 'class_12_percentile', 'operator' => '>=', 'value' => 80, 'label' => 'Must be in top 20% in Class XII board'],
                    ['field' => 'annual_income', 'operator' => '<=', 'value' => 800000, 'label' => 'Family annual income ≤ ₹8,00,000'],
                    ['field' => 'age', 'operator' => '<=', 'value' => 25, 'label' => 'Must be 25 years or younger'],
                ],
                'required_documents' => ['Class XII Marksheet', 'Income Certificate', 'Admission Letter', 'Aadhaar Card', 'Bank Passbook'],
                'application_url' => 'https://scholarships.gov.in',
                'tags' => ['scholarship', 'merit', 'college', 'education'],
            ],

            // ─── Health ───────────────────────────────────────────────────────────────
            [
                'slug'     => 'health-medical',
                'title'    => 'Ayushman Bharat PM-JAY',
                'ministry' => 'Ministry of Health & Family Welfare',
                'description' => 'World\'s largest health assurance scheme providing coverage of ₹5 lakh per family per year for secondary and tertiary hospitalization.',
                'benefits' => ['Health cover of ₹5 lakh per family per year', 'Cashless treatment at empaneled hospitals', '1,393+ treatment packages covered', 'Pre & post hospitalization coverage'],
                'eligibility_rules' => [
                    ['field' => 'annual_income', 'operator' => '<=', 'value' => 100000, 'label' => 'Must be from a low-income household'],
                    ['field' => 'secc_listed', 'operator' => '==', 'value' => true, 'label' => 'Must be listed in SECC 2011 database'],
                ],
                'required_documents' => ['Aadhaar Card', 'Ration Card', 'Income Certificate'],
                'application_url' => 'https://pmjay.gov.in',
                'tags' => ['health', 'insurance', 'hospital', 'ayushman', 'family'],
            ],
            [
                'slug'     => 'health-medical',
                'title'    => 'Janani Suraksha Yojana (JSY)',
                'ministry' => 'Ministry of Health & Family Welfare',
                'description' => 'Safe motherhood intervention promoting institutional delivery among poor pregnant women. Provides cash assistance to pregnant women.',
                'benefits' => ['₹1,400 cash incentive in rural areas', '₹1,000 in urban areas', 'Incentive for ASHA workers', 'Free delivery at government hospitals'],
                'eligibility_rules' => [
                    ['field' => 'gender', 'operator' => '==', 'value' => 'female', 'label' => 'Must be female'],
                    ['field' => 'is_pregnant', 'operator' => '==', 'value' => true, 'label' => 'Must be pregnant'],
                    ['field' => 'annual_income', 'operator' => '<=', 'value' => 100000, 'label' => 'Below Poverty Line (BPL) family'],
                    ['field' => 'age', 'operator' => '>=', 'value' => 18, 'label' => 'Must be at least 18 years old'],
                ],
                'required_documents' => ['Aadhaar Card', 'BPL Card/Ration Card', 'ANC Card', 'Bank Account'],
                'application_url' => 'https://nhm.gov.in',
                'tags' => ['maternal', 'health', 'women', 'pregnancy', 'bpl'],
            ],

            // ─── Women Empowerment ────────────────────────────────────────────────────
            [
                'slug'     => 'women-empowerment',
                'title'    => 'Beti Bachao Beti Padhao',
                'ministry' => 'Ministry of Women & Child Development',
                'description' => 'Scheme to address declining Child Sex Ratio and promote welfare and education of the girl child.',
                'benefits' => ['Sukanya Samriddhi Account benefits', 'Conditional cash transfer for girl education', 'Awareness campaigns'],
                'eligibility_rules' => [
                    ['field' => 'gender', 'operator' => '==', 'value' => 'female', 'label' => 'Must be female (girl child)'],
                    ['field' => 'age', 'operator' => '<=', 'value' => 10, 'label' => 'Must be 10 years or younger for Sukanya Samriddhi'],
                ],
                'required_documents' => ['Birth Certificate', 'Parent Aadhaar Card', 'Bank Account (Parent)'],
                'application_url' => 'https://wcd.nic.in',
                'tags' => ['girl child', 'women', 'education', 'beti bachao'],
            ],
            [
                'slug'     => 'women-empowerment',
                'title'    => 'Pradhan Mantri Matru Vandana Yojana (PMMVY)',
                'ministry' => 'Ministry of Women & Child Development',
                'description' => 'Maternity benefit program providing ₹5,000 in three installments to pregnant women and lactating mothers for first living child.',
                'benefits' => ['₹5,000 cash benefit in three installments', 'Wage compensation for wage loss', 'Improved health and nutrition'],
                'eligibility_rules' => [
                    ['field' => 'gender', 'operator' => '==', 'value' => 'female', 'label' => 'Must be female'],
                    ['field' => 'is_pregnant', 'operator' => '==', 'value' => true, 'label' => 'Must be pregnant (first child)'],
                    ['field' => 'age', 'operator' => '>=', 'value' => 19, 'label' => 'Must be 19 years or older'],
                ],
                'required_documents' => ['Aadhaar Card', 'MCP Card', 'Bank Account', 'Marriage Certificate'],
                'application_url' => 'https://wcd.nic.in',
                'tags' => ['maternity', 'women', 'pregnancy', 'cash benefit'],
            ],

            // ─── Housing ──────────────────────────────────────────────────────────────
            [
                'slug'     => 'housing-urban-development',
                'title'    => 'PM Awas Yojana - Gramin (PMAY-G)',
                'ministry' => 'Ministry of Rural Development',
                'description' => 'Provides financial assistance for construction of pucca houses with basic amenities to homeless and kutcha/dilapidated house dwellers in rural areas.',
                'benefits' => ['₹1.20 lakh in plain areas', '₹1.30 lakh in hilly/difficult areas', 'MGNREGA wages for construction', 'Toilet support under SBM'],
                'eligibility_rules' => [
                    ['field' => 'residence_type', 'operator' => '==', 'value' => 'rural', 'label' => 'Must reside in rural area'],
                    ['field' => 'house_condition', 'operator' => 'in', 'value' => ['homeless', 'kutcha', 'dilapidated'], 'label' => 'Must be homeless or living in kutcha/dilapidated house'],
                    ['field' => 'annual_income', 'operator' => '<=', 'value' => 150000, 'label' => 'Annual income ≤ ₹1,50,000'],
                ],
                'required_documents' => ['Aadhaar Card', 'SECC Data Verification', 'Bank Account', 'Land Documents'],
                'application_url' => 'https://pmayg.nic.in',
                'tags' => ['housing', 'rural', 'shelter', 'pucca house'],
            ],

            // ─── Finance ─────────────────────────────────────────────────────────────
            [
                'slug'     => 'finance-banking',
                'title'    => 'PM Mudra Yojana (PMMY)',
                'ministry' => 'Ministry of Finance',
                'description' => 'Provides loans up to ₹10 lakh to non-corporate, non-farm small/micro enterprises under three categories: Shishu, Kishor, Tarun.',
                'benefits' => ['Shishu: Loans up to ₹50,000', 'Kishor: ₹50,001 to ₹5 lakh', 'Tarun: ₹5 lakh to ₹10 lakh', 'No collateral required'],
                'eligibility_rules' => [
                    ['field' => 'business_type', 'operator' => 'not_in', 'value' => ['corporate', 'farm'], 'label' => 'Must be non-corporate, non-farm business'],
                    ['field' => 'age', 'operator' => '>=', 'value' => 18, 'label' => 'Must be at least 18 years old'],
                    ['field' => 'has_credit_history', 'operator' => '!=', 'value' => 'defaulter', 'label' => 'Must not be a loan defaulter'],
                ],
                'required_documents' => ['Aadhaar Card', 'PAN Card', 'Business Plan', 'Bank Statements (6 months)', 'Proof of Business'],
                'application_url' => 'https://mudra.org.in',
                'tags' => ['loan', 'micro enterprise', 'mudra', 'business', 'self-employment'],
            ],

            // ─── Social Welfare ───────────────────────────────────────────────────────
            [
                'slug'     => 'social-welfare',
                'title'    => 'National Social Assistance Programme (NSAP)',
                'ministry' => 'Ministry of Rural Development',
                'description' => 'Social security and welfare program covering old age pension, widow pension, disability pension, and family benefit for BPL households.',
                'benefits' => ['₹200-500/month old age pension', '₹300/month widow pension', '₹300/month disability pension', '₹20,000 family benefit on death'],
                'eligibility_rules' => [
                    ['field' => 'annual_income', 'operator' => '<=', 'value' => 100000, 'label' => 'Must be BPL (Below Poverty Line)'],
                    ['field' => 'age', 'operator' => '>=', 'value' => 60, 'label' => 'Must be 60 years or older (for old age pension)'],
                ],
                'required_documents' => ['Aadhaar Card', 'BPL Certificate', 'Age Proof', 'Bank Account'],
                'application_url' => 'https://nsap.nic.in',
                'tags' => ['pension', 'elderly', 'widow', 'disability', 'social security', 'bpl'],
            ],

            // ─── Employment ───────────────────────────────────────────────────────────
            [
                'slug'     => 'employment-skill',
                'title'    => 'Mahatma Gandhi NREGA (MGNREGS)',
                'ministry' => 'Ministry of Rural Development',
                'description' => 'Guarantees at least 100 days of wage employment per year to every rural household whose adult members volunteer to do unskilled manual work.',
                'benefits' => ['100 days guaranteed wage employment', 'Minimum wage as per state', 'Work within 5 km of residence', 'Unemployment allowance if work not provided'],
                'eligibility_rules' => [
                    ['field' => 'residence_type', 'operator' => '==', 'value' => 'rural', 'label' => 'Must reside in rural area'],
                    ['field' => 'age', 'operator' => '>=', 'value' => 18, 'label' => 'Must be an adult (18+)'],
                    ['field' => 'willing_for_manual_work', 'operator' => '==', 'value' => true, 'label' => 'Must be willing to do unskilled manual work'],
                ],
                'required_documents' => ['Aadhaar Card', 'Job Card Application', 'Bank/Post Office Account', 'Residence Proof'],
                'application_url' => 'https://nrega.nic.in',
                'tags' => ['employment', 'rural', 'wage', 'mgnrega', 'nrega'],
            ],

            // ─── Digital India ────────────────────────────────────────────────────────
            [
                'slug'     => 'digital-india',
                'title'    => 'PM Wani (Wi-Fi Access Network Interface)',
                'ministry' => 'Ministry of Communications',
                'description' => 'Enables proliferation of broadband through Public Data Offices (PDOs) to provide affordable Wi-Fi services to citizens.',
                'benefits' => ['Affordable public Wi-Fi access', 'Digital connectivity in rural/semi-urban areas', 'Opportunity to become PDO provider'],
                'eligibility_rules' => [
                    ['field' => 'age', 'operator' => '>=', 'value' => 18, 'label' => 'Must be 18 years or older'],
                ],
                'required_documents' => ['Aadhaar Card', 'PAN Card'],
                'application_url' => 'https://dot.gov.in',
                'tags' => ['wifi', 'digital', 'connectivity', 'broadband'],
            ],

            // ─── Environment ──────────────────────────────────────────────────────────
            [
                'slug'     => 'environment-energy',
                'title'    => 'PM Ujjwala Yojana (PMUY)',
                'ministry' => 'Ministry of Petroleum & Natural Gas',
                'description' => 'Provides free LPG connections to women from BPL households to safeguard health and ensure clean cooking fuel.',
                'benefits' => ['Free LPG connection', 'Deposit waiver', 'Free first cylinder refill', 'Stove and regulator support'],
                'eligibility_rules' => [
                    ['field' => 'gender', 'operator' => '==', 'value' => 'female', 'label' => 'Must be female (adult woman)'],
                    ['field' => 'age', 'operator' => '>=', 'value' => 18, 'label' => 'Must be 18 years or older'],
                    ['field' => 'annual_income', 'operator' => '<=', 'value' => 100000, 'label' => 'Must be from BPL household'],
                    ['field' => 'has_lpg_connection', 'operator' => '==', 'value' => false, 'label' => 'Must not already have an LPG connection'],
                ],
                'required_documents' => ['Aadhaar Card', 'BPL Ration Card', 'Bank Account', 'Passport Photo'],
                'application_url' => 'https://pmuy.gov.in',
                'tags' => ['lpg', 'cooking', 'women', 'clean energy', 'bpl', 'ujjwala'],
            ],
        ];

        foreach ($schemes as $schemeData) {
            $categorySlug = $schemeData['slug'];
            unset($schemeData['slug']);

            $category = $cats->get($categorySlug);

            Scheme::updateOrCreate(
                ['title' => $schemeData['title']],
                array_merge($schemeData, [
                    'category_id' => $category ? (string) $category->_id : null,
                    'is_active'   => true,
                ])
            );
        }

        $this->command->info('Schemes seeded: '.count($schemes));
    }
}
