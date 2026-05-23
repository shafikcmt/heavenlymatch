<?php

namespace Database\Seeders;

use App\Models\Biodata;
use App\Models\Registration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DummyMatrimonySeeder extends Seeder
{
    private string $password = 'password123';

    public function run(): void
    {
        if (! Schema::hasTable('registrations') || ! Schema::hasTable('biodatas')) {
            $this->command?->warn('registrations or biodatas table missing. Run php artisan migrate first.');
            return;
        }

        foreach ($this->profiles() as $index => $profile) {
            $registrationId = 'HM9' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT);
            $email          = $profile['email'];
            $gender         = $profile['gender'];
            $isMale         = $gender === 'male';

            $registrationData = [
                'registration_id'      => $registrationId,
                'name'                 => $profile['name'],
                'gender'               => $gender,
                'profile_created_for'  => $profile['profile_created_for'],
                'looking_for'          => $isMale ? 'bride' : 'groom',
                'platform_mode'        => 'general',
                'preferred_language'   => 'bn',
                'email'                => $email,
                'is_email_verified'    => true,
                'is_mobile_verified'   => true,
                'country_code'         => '+880',
                'mobile_number'        => $profile['mobile'],
                'password'             => Hash::make($this->password),
                'terms_accepted_at'    => now(),
                'last_login_at'        => now()->subDays(rand(1, 20)),
            ];

            $user = Registration::where('email', $email)->first();

            if (! $user) {
                $user = Registration::create($this->onlyExistingColumns('registrations', $registrationData));
                $user->forceFill(['account_status' => 'active', 'email_verified_at' => now()])->save();
            } else {
                $updateData = $registrationData;
                unset($updateData['registration_id'], $updateData['email']);
                $user->fill($this->onlyExistingColumns('registrations', $updateData))->save();
            }

            // Build education_details JSON from per-profile data
            $educationDetails = array_filter([
                'graduation' => ($profile['graduation_subject'] || $profile['graduation_institution'] || $profile['graduation_year'])
                    ? [
                        'subject'     => $profile['graduation_subject'],
                        'institution' => $profile['graduation_institution'],
                        'year'        => $profile['graduation_year'],
                      ]
                    : null,
                'post_graduation' => ($profile['postgraduation_subject'] || $profile['postgraduation_institution'])
                    ? [
                        'subject'     => $profile['postgraduation_subject'],
                        'institution' => $profile['postgraduation_institution'],
                        'year'        => $profile['postgraduation_year'],
                      ]
                    : null,
                'islamic' => ($profile['islamic_titles'] || $profile['islamic_institution'])
                    ? [
                        'titles'      => $profile['islamic_titles'],
                        'institution' => $profile['islamic_institution'],
                        'year'        => $profile['islamic_year'],
                      ]
                    : null,
            ]);

            [$partnerAgeMin, $partnerAgeMax]             = $this->parsePartnerAge($profile['partner_age']);
            [$partnerHeightMin, $partnerHeightMax]        = $this->parsePartnerHeight($profile['partner_height']);

            $biodata = array_merge($this->baseBiodata($profile), [
                'registration_id'        => $user->registration_id,
                'birth_date'             => $profile['birth_date'],
                'height_cm'              => $this->heightToCm($profile['height']),
                'weight_kg'              => $this->weightToKg($profile['weight']),
                'complexion'             => $this->mapComplexion($profile['complexion']),
                'blood_group'            => $profile['blood_group'],
                'division'               => $profile['division'],
                'district'               => $profile['district'],
                'present_address'        => $profile['present_address'],
                'grew_up_in'             => $profile['grew_up'],
                'village_area'           => $profile['area'],
                'highest_qualification'  => $this->mapQualification($profile['qualification']),
                'education_method'       => $this->mapEducationMethod($profile['education_method']),
                'education_details'      => ! empty($educationDetails) ? json_encode($educationDetails) : null,
                'occupation'             => $profile['occupation'],
                'monthly_income'         => $profile['income'],
                'profession_details'     => $profile['profession_details'],
                'family_financial_status'=> $this->mapFinancialStatus($profile['financial_status']),
                'father_name'            => $profile['father_name'],
                'father_profession'      => $profile['father_profession'],
                'mother_name'            => $profile['mother_name'],
                'partner_age_min'        => $partnerAgeMin,
                'partner_age_max'        => $partnerAgeMax,
                'partner_height_cm_min'  => $partnerHeightMin,
                'partner_height_cm_max'  => $partnerHeightMax,
                'partner_education'      => $profile['partner_education'],
                'partner_district'       => $profile['partner_district'],
                'guardian_mobile'        => $profile['guardian_mobile'],
                'guardian_email'         => 'guardian.' . Str::slug($profile['name'], '.') . '@example.com',
                'status'                 => $profile['approval_status'],
            ]);

            Biodata::updateOrCreate(
                ['registration_id' => $user->registration_id],
                $this->onlyExistingColumns('biodatas', $biodata)
            );
        }

        $this->command?->info('Dummy profiles seeded. Demo password: ' . $this->password);
    }

    private function onlyExistingColumns(string $table, array $data): array
    {
        $columns = Schema::getColumnListing($table);

        return collect($data)
            ->filter(fn ($value, $key) => in_array($key, $columns, true))
            ->all();
    }

    private function baseBiodata(array $profile): array
    {
        $isMale = $profile['gender'] === 'male';

        return [
            'marital_status'           => 'never_married',
            'children_count'           => 0,
            'nationality'              => 'Bangladeshi',
            'religion'                 => 'Islam',
            'sect'                     => 'Hanafi',
            'is_practicing'            => true,
            'accepts_interfaith'       => false,
            'prayers_info'             => '5_times',
            'quran_recitation'         => 'basic',
            'fiqh'                     => 'Hanafi',
            'clothing_style'           => $isMale
                ? 'Modest — tries to maintain Islamic dress'
                : 'Borka and hijab outside',
            'beard_info'               => $isMale ? 'Keeps beard per sunnah' : null,
            'hijab_info'               => $isMale ? null : 'Wears hijab',
            'is_islamically_educated'  => false,
            'beliefs_on_mazar'         => 'Respects scholars but avoids anything conflicting with tawheed.',
            'favorite_scholars'        => 'Mufti Taqi Usmani, Dr. Abdullah Jahangir',
            'religious_work'           => 'Attends Islamic talks and local learning circles.',
            'watch_entertainment'      => 'Avoids harmful entertainment.',
            'hobbies'                  => 'Reading, family time, learning new skills.',
            'health_status'            => 'healthy',
            'diet'                     => 'halal_only',
            'smoking'                  => 'never',
            'father_alive'             => true,
            'mother_alive'             => true,
            'brothers'                 => $profile['brothers'],
            'sisters'                  => $profile['sisters'],
            'family_details'           => 'Educated, respectful and marriage-focused family.',
            'family_religious_condition'=> 'Family maintains prayers and halal income.',
            'home_ownership'           => 'family_house',
            'guardian_agree'           => true,
            'wife_in_veil'             => $isMale ? true : null,
            'wife_study_allowed'       => $isMale ? true : null,
            'wife_job_allowed'         => $isMale ? true : null,
            'residence_after_marriage' => 'Decided with family consultation.',
            'expect_gift_from_bride'   => null,
            'polygamy_open'            => false,
            'post_marriage_plan'       => 'flexible',
            'profession_halal_status'  => 'halal',
            'partner_complexion'       => null,
            'partner_marital_status'   => 'never_married',
            'partner_expectations'     => 'Practicing Muslim, honest, family-oriented and serious about marriage.',
            'guardian_relationship'    => $isMale ? 'Father' : 'Brother',
            'is_completed'             => true,
            'completeness_score'       => $profile['approval_status'] === 'approved' ? 88 : 62,
        ];
    }

    // ── Data-mapping helpers ──────────────────────────────────────────────────

    private function heightToCm(string $height): ?int
    {
        // Parses "5 ft 8 in" → 173
        if (preg_match('/(\d+)\s*ft\s*(\d+)\s*in/i', $height, $m)) {
            return (int) round((int) $m[1] * 30.48 + (int) $m[2] * 2.54);
        }

        return null;
    }

    private function weightToKg(string $weight): ?int
    {
        // Parses "70 kg" → 70
        if (preg_match('/(\d+)\s*kg/i', $weight, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    private function mapComplexion(string $value): string
    {
        return match (strtolower(trim($value))) {
            'bright', 'bright fair', 'very fair' => 'fair',
            'fair'                                => 'fair',
            'medium fair', 'wheatish'            => 'wheatish',
            'medium'                              => 'medium',
            'dark'                                => 'dark',
            default                               => 'medium',
        };
    }

    private function mapQualification(string $value): string
    {
        $v = strtolower($value);

        if (str_contains($v, 'phd'))                         return 'phd';
        if (str_contains($v, 'mba') || str_contains($v, 'msc') || str_contains($v, 'mss') || str_contains($v, 'post'))
                                                              return 'post_graduation';
        if (str_contains($v, 'bsc') || str_contains($v, 'ba ') || str_contains($v, 'bba') || str_contains($v, 'bss') || str_contains($v, 'mbbs') || str_contains($v, 'graduation'))
                                                              return 'graduation';
        if (str_contains($v, 'diploma'))                     return 'diploma';
        if (str_contains($v, 'kamil'))                       return 'kamil';
        if (str_contains($v, 'fazil'))                       return 'fazil';
        if (str_contains($v, 'alim'))                        return 'alim';
        if (str_contains($v, 'hafez') || str_contains($v, 'hafiza')) return 'hafez';
        if (str_contains($v, 'hsc'))                         return 'hsc';
        if (str_contains($v, 'ssc'))                         return 'ssc';

        return 'graduation';
    }

    private function mapFinancialStatus(string $value): string
    {
        return match (strtolower(trim($value))) {
            'upper class', 'upper'                 => 'upper',
            'upper middle class', 'upper middle'   => 'upper_middle',
            'middle class', 'middle'               => 'middle',
            'lower middle class', 'lower middle'   => 'lower_middle',
            'lower class', 'lower'                 => 'lower',
            default                                => 'middle',
        };
    }

    private function mapEducationMethod(string $value): string
    {
        $v = strtolower($value);

        if (str_contains($v, 'both') || (str_contains($v, 'general') && str_contains($v, 'islamic'))) {
            return 'both';
        }

        if (str_contains($v, 'alia') || str_contains($v, 'qawmi') || str_contains($v, 'islamic')) {
            return 'islamic';
        }

        return 'general';
    }

    /** Returns [min, max] integers or [null, null]. Parses "20-26", "26-34", "22-32" etc. */
    private function parsePartnerAge(string $value): array
    {
        if (preg_match('/(\d+)\s*[-–]\s*(\d+)/', $value, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }

        return [null, null];
    }

    /** Returns [min_cm, max_cm] integers or [null, null]. Parses "5 ft 0 in - 5 ft 6 in", "5 ft 5 in+" etc. */
    private function parsePartnerHeight(string $value): array
    {
        // Range: "5 ft 0 in - 5 ft 6 in"
        if (preg_match('/(\d+)\s*ft\s*(\d+)\s*in\s*[-–]\s*(\d+)\s*ft\s*(\d+)\s*in/i', $value, $m)) {
            $min = (int) round((int) $m[1] * 30.48 + (int) $m[2] * 2.54);
            $max = (int) round((int) $m[3] * 30.48 + (int) $m[4] * 2.54);

            return [$min, $max];
        }

        // Minimum only: "5 ft 5 in+"
        if (preg_match('/(\d+)\s*ft\s*(\d+)\s*in\s*\+/i', $value, $m)) {
            $min = (int) round((int) $m[1] * 30.48 + (int) $m[2] * 2.54);

            return [$min, null];
        }

        return [null, null];
    }

    // ── Profile data ──────────────────────────────────────────────────────────

    private function profiles(): array
    {
        return [
            [
                'name' => 'Md Shafiqul Islam', 'email' => 'demo.groom@heavenlymatch.test',
                'mobile' => '01710000001', 'guardian_mobile' => '01710001001',
                'profile_created_for' => 'self', 'gender' => 'male',
                'birth_date' => '1996-04-18', 'height' => '5 ft 8 in', 'weight' => '70 kg',
                'complexion' => 'Bright', 'blood_group' => 'B+',
                'division' => 'Dhaka', 'district' => 'Dhaka', 'present_address' => 'Mirpur, Dhaka',
                'grew_up' => 'Dhaka', 'area' => 'Mirpur 10',
                'qualification' => 'BSc in Computer Science',
                'graduation_subject' => 'Computer Science', 'graduation_institution' => 'Daffodil International University', 'graduation_year' => '2019',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General + Islamic',
                'islamic_titles' => null, 'islamic_institution' => 'Local masjid maktab', 'islamic_year' => '2010',
                'occupation' => 'Software Engineer', 'income' => 85000,
                'profession_details' => 'Works as a software engineer in a private IT company in Dhaka.',
                'financial_status' => 'Upper Middle Class',
                'father_name' => 'Md Abdul Karim', 'father_profession' => 'Retired government service holder.',
                'mother_name' => 'Mst Ayesha Begum',
                'brothers' => 1, 'sisters' => 1,
                'partner_age' => '20-26', 'partner_height' => '5 ft 0 in - 5 ft 6 in',
                'partner_education' => 'HSC/Bachelor or Islamic education',
                'partner_district' => 'Dhaka, Gazipur, Narayanganj',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Nusrat Jahan', 'email' => 'demo.bride@heavenlymatch.test',
                'mobile' => '01710000002', 'guardian_mobile' => '01710001002',
                'profile_created_for' => 'daughter', 'gender' => 'female',
                'birth_date' => '1999-08-12', 'height' => '5 ft 3 in', 'weight' => '55 kg',
                'complexion' => 'Fair', 'blood_group' => 'O+',
                'division' => 'Chattogram', 'district' => 'Chattogram', 'present_address' => 'Panchlaish, Chattogram',
                'grew_up' => 'Chattogram', 'area' => 'Panchlaish',
                'qualification' => 'BA in English',
                'graduation_subject' => 'English', 'graduation_institution' => 'University of Chittagong', 'graduation_year' => '2021',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General + Islamic',
                'islamic_titles' => null, 'islamic_institution' => 'Home Quran teacher', 'islamic_year' => '2012',
                'occupation' => 'Teacher', 'income' => 25000,
                'profession_details' => 'Works as a teacher at a girls school.',
                'financial_status' => 'Middle Class',
                'father_name' => 'Mohammad Harun', 'father_profession' => 'Small business owner.',
                'mother_name' => 'Fatema Khatun',
                'brothers' => 2, 'sisters' => 0,
                'partner_age' => '26-34', 'partner_height' => '5 ft 5 in+',
                'partner_education' => 'Bachelor or above / Alim or above',
                'partner_district' => 'Chattogram, Dhaka, Cumilla',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Abdullah Al Mamun', 'email' => 'abdullah.mamun@heavenlymatch.test',
                'mobile' => '01710000003', 'guardian_mobile' => '01710001003',
                'profile_created_for' => 'self', 'gender' => 'male',
                'birth_date' => '1993-01-20', 'height' => '5 ft 7 in', 'weight' => '68 kg',
                'complexion' => 'Medium', 'blood_group' => 'A+',
                'division' => 'Rajshahi', 'district' => 'Rajshahi', 'present_address' => 'Shaheb Bazar, Rajshahi',
                'grew_up' => 'Rajshahi', 'area' => 'Shaheb Bazar',
                'qualification' => 'MBA',
                'graduation_subject' => 'Management', 'graduation_institution' => 'University of Rajshahi', 'graduation_year' => '2016',
                'postgraduation_subject' => 'MBA', 'postgraduation_institution' => 'University of Rajshahi', 'postgraduation_year' => '2018',
                'education_method' => 'General',
                'islamic_titles' => null, 'islamic_institution' => 'Local madrasa evening course', 'islamic_year' => '2013',
                'occupation' => 'Bank Operations Officer', 'income' => 62000,
                'profession_details' => 'Works in banking operations.',
                'financial_status' => 'Middle Class',
                'father_name' => 'Md Jalal Uddin', 'father_profession' => 'Grocery wholesale business owner.',
                'mother_name' => 'Rokeya Begum',
                'brothers' => 1, 'sisters' => 2,
                'partner_age' => '22-29', 'partner_height' => '5 ft 0 in+',
                'partner_education' => 'Graduate or madrasa educated',
                'partner_district' => 'Rajshahi, Bogura, Naogaon',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Sumaiya Akter', 'email' => 'sumaiya.akter@heavenlymatch.test',
                'mobile' => '01710000004', 'guardian_mobile' => '01710001004',
                'profile_created_for' => 'sister', 'gender' => 'female',
                'birth_date' => '2001-03-08', 'height' => '5 ft 1 in', 'weight' => '49 kg',
                'complexion' => 'Bright Fair', 'blood_group' => 'AB+',
                'division' => 'Sylhet', 'district' => 'Sylhet', 'present_address' => 'Zindabazar, Sylhet',
                'grew_up' => 'Sylhet', 'area' => 'Zindabazar',
                'qualification' => 'HSC',
                'graduation_subject' => 'Science', 'graduation_institution' => 'Sylhet Government College', 'graduation_year' => null,
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General + Islamic',
                'islamic_titles' => 'Hafiza', 'islamic_institution' => 'Local Hifz Madrasa', 'islamic_year' => '2018',
                'occupation' => 'Student', 'income' => 0,
                'profession_details' => 'Currently studying and helping family at home.',
                'financial_status' => 'Middle Class',
                'father_name' => 'Abdul Matin', 'father_profession' => 'Expatriate worker in Saudi Arabia.',
                'mother_name' => 'Jannatul Ferdous',
                'brothers' => 1, 'sisters' => 1,
                'partner_age' => '25-32', 'partner_height' => '5 ft 5 in+',
                'partner_education' => 'Graduate / Alim / Qawmi',
                'partner_district' => 'Sylhet, Dhaka, Moulvibazar',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Mahmudul Hasan', 'email' => 'mahmudul.hasan@heavenlymatch.test',
                'mobile' => '01710000005', 'guardian_mobile' => '01710001005',
                'profile_created_for' => 'self', 'gender' => 'male',
                'birth_date' => '1994-11-05', 'height' => '5 ft 10 in', 'weight' => '76 kg',
                'complexion' => 'Fair', 'blood_group' => 'O-',
                'division' => 'Khulna', 'district' => 'Khulna', 'present_address' => 'Sonadanga, Khulna',
                'grew_up' => 'Khulna', 'area' => 'Sonadanga',
                'qualification' => 'Diploma Engineer',
                'graduation_subject' => 'Civil Engineering', 'graduation_institution' => 'Khulna Polytechnic Institute', 'graduation_year' => '2015',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General',
                'islamic_titles' => null, 'islamic_institution' => 'Masjid based classes', 'islamic_year' => '2009',
                'occupation' => 'Civil Engineer', 'income' => 70000,
                'profession_details' => 'Works in private construction consultancy.',
                'financial_status' => 'Upper Middle Class',
                'father_name' => 'Md Abdur Rahman', 'father_profession' => 'Owns agricultural land.',
                'mother_name' => 'Saleha Begum',
                'brothers' => 0, 'sisters' => 2,
                'partner_age' => '20-27', 'partner_height' => '5 ft 1 in+',
                'partner_education' => 'HSC or above',
                'partner_district' => 'Khulna, Jashore, Satkhira',
                'approval_status' => 'pending',
            ],
            [
                'name' => 'Ayesha Siddika', 'email' => 'ayesha.siddika@heavenlymatch.test',
                'mobile' => '01710000006', 'guardian_mobile' => '01710001006',
                'profile_created_for' => 'daughter', 'gender' => 'female',
                'birth_date' => '1998-06-25', 'height' => '5 ft 4 in', 'weight' => '58 kg',
                'complexion' => 'Medium Fair', 'blood_group' => 'A-',
                'division' => 'Barishal', 'district' => 'Barishal', 'present_address' => 'Nathullabad, Barishal',
                'grew_up' => 'Barishal', 'area' => 'Nathullabad',
                'qualification' => 'BSS in Economics',
                'graduation_subject' => 'Economics', 'graduation_institution' => 'University of Barishal', 'graduation_year' => '2020',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General + Islamic',
                'islamic_titles' => null, 'islamic_institution' => 'Home Islamic study', 'islamic_year' => '2014',
                'occupation' => 'Homemaker', 'income' => 0,
                'profession_details' => 'Prefers family life and learning useful skills from home.',
                'financial_status' => 'Middle Class',
                'father_name' => 'Md Shah Alam', 'father_profession' => 'Private service holder.',
                'mother_name' => 'Nasima Begum',
                'brothers' => 1, 'sisters' => 1,
                'partner_age' => '27-35', 'partner_height' => '5 ft 5 in+',
                'partner_education' => 'Graduate or stable halal profession',
                'partner_district' => 'Barishal, Dhaka, Khulna',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Mizanur Rahman', 'email' => 'mizanur.rahman@heavenlymatch.test',
                'mobile' => '01710000007', 'guardian_mobile' => '01710001007',
                'profile_created_for' => 'self', 'gender' => 'male',
                'birth_date' => '1991-09-17', 'height' => '5 ft 6 in', 'weight' => '64 kg',
                'complexion' => 'Medium', 'blood_group' => 'B-',
                'division' => 'Mymensingh', 'district' => 'Mymensingh', 'present_address' => 'Charpara, Mymensingh',
                'grew_up' => 'Mymensingh', 'area' => 'Charpara',
                'qualification' => 'Alim',
                'graduation_subject' => 'Islamic Studies', 'graduation_institution' => 'Ananda Mohan College', 'graduation_year' => '2015',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'Alia',
                'islamic_titles' => 'Mawlana', 'islamic_institution' => 'Alia Madrasa', 'islamic_year' => '2011',
                'occupation' => 'Madrasa Teacher', 'income' => 38000,
                'profession_details' => 'Teaches Islamic studies in a madrasa.',
                'financial_status' => 'Middle Class',
                'father_name' => 'Md Nurul Islam', 'father_profession' => 'Retired school teacher.',
                'mother_name' => 'Rahima Khatun',
                'brothers' => 2, 'sisters' => 1,
                'partner_age' => '22-30', 'partner_height' => '5 ft 0 in+',
                'partner_education' => 'Madrasa educated preferred',
                'partner_district' => 'Mymensingh, Netrokona, Kishoreganj',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Farhana Tasnim', 'email' => 'farhana.tasnim@heavenlymatch.test',
                'mobile' => '01710000008', 'guardian_mobile' => '01710001008',
                'profile_created_for' => 'relative', 'gender' => 'female',
                'birth_date' => '2000-12-14', 'height' => '5 ft 2 in', 'weight' => '52 kg',
                'complexion' => 'Fair', 'blood_group' => 'AB-',
                'division' => 'Chattogram', 'district' => 'Cumilla', 'present_address' => 'Kandirpar, Cumilla',
                'grew_up' => 'Cumilla', 'area' => 'Kandirpar',
                'qualification' => 'BBA',
                'graduation_subject' => 'Accounting', 'graduation_institution' => 'Cumilla Victoria College', 'graduation_year' => '2022',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General',
                'islamic_titles' => null, 'islamic_institution' => 'Weekend Islamic course', 'islamic_year' => '2016',
                'occupation' => 'Student', 'income' => 0,
                'profession_details' => 'Preparing for higher studies and learning Quran translation.',
                'financial_status' => 'Middle Class',
                'father_name' => 'Md Yusuf Ali', 'father_profession' => 'Hardware business owner.',
                'mother_name' => 'Momena Begum',
                'brothers' => 1, 'sisters' => 2,
                'partner_age' => '26-33', 'partner_height' => '5 ft 5 in+',
                'partner_education' => 'BBA/MBA/Engineering or Islamic education',
                'partner_district' => 'Cumilla, Dhaka, Feni',
                'approval_status' => 'pending',
            ],
            [
                'name' => 'Tanvir Ahmed', 'email' => 'tanvir.ahmed@heavenlymatch.test',
                'mobile' => '01710000009', 'guardian_mobile' => '01710001009',
                'profile_created_for' => 'self', 'gender' => 'male',
                'birth_date' => '1997-02-03', 'height' => '5 ft 9 in', 'weight' => '73 kg',
                'complexion' => 'Bright', 'blood_group' => 'O+',
                'division' => 'Dhaka', 'district' => 'Narayanganj', 'present_address' => 'Fatullah, Narayanganj',
                'grew_up' => 'Narayanganj', 'area' => 'Fatullah',
                'qualification' => 'BSc Textile Engineering',
                'graduation_subject' => 'Textile Engineering', 'graduation_institution' => 'BUTEX', 'graduation_year' => '2020',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General',
                'islamic_titles' => null, 'islamic_institution' => 'Local masjid halaqa', 'islamic_year' => '2011',
                'occupation' => 'Textile Engineer', 'income' => 78000,
                'profession_details' => 'Works in garments manufacturing.',
                'financial_status' => 'Upper Middle Class',
                'father_name' => 'Md Habibur Rahman', 'father_profession' => 'Transport business owner.',
                'mother_name' => 'Shamsun Nahar',
                'brothers' => 1, 'sisters' => 0,
                'partner_age' => '21-27', 'partner_height' => '5 ft 1 in+',
                'partner_education' => 'Graduate preferred',
                'partner_district' => 'Narayanganj, Dhaka, Munshiganj',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Mariya Islam', 'email' => 'mariya.islam@heavenlymatch.test',
                'mobile' => '01710000010', 'guardian_mobile' => '01710001010',
                'profile_created_for' => 'daughter', 'gender' => 'female',
                'birth_date' => '1997-07-07', 'height' => '5 ft 5 in', 'weight' => '60 kg',
                'complexion' => 'Bright Fair', 'blood_group' => 'B+',
                'division' => 'Rajshahi', 'district' => 'Bogura', 'present_address' => 'Jaleshwaritola, Bogura',
                'grew_up' => 'Bogura', 'area' => 'Jaleshwaritola',
                'qualification' => 'MBBS',
                'graduation_subject' => 'Medicine', 'graduation_institution' => 'Shaheed Ziaur Rahman Medical College', 'graduation_year' => '2022',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General + Islamic',
                'islamic_titles' => null, 'islamic_institution' => 'Online Islamic course', 'islamic_year' => '2017',
                'occupation' => 'Doctor', 'income' => 45000,
                'profession_details' => 'Intern doctor in a women-friendly healthcare setting.',
                'financial_status' => 'Upper Middle Class',
                'father_name' => 'Dr. Mahbub Alam', 'father_profession' => 'Senior doctor in a private hospital.',
                'mother_name' => 'Sharmin Akter',
                'brothers' => 0, 'sisters' => 1,
                'partner_age' => '28-36', 'partner_height' => '5 ft 6 in+',
                'partner_education' => 'Doctor/Engineer/Graduate practicing Muslim',
                'partner_district' => 'Bogura, Dhaka, Rajshahi',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Rafiqul Islam', 'email' => 'rafiqul.islam@heavenlymatch.test',
                'mobile' => '01710000011', 'guardian_mobile' => '01710001011',
                'profile_created_for' => 'self', 'gender' => 'male',
                'birth_date' => '1989-10-28', 'height' => '5 ft 6 in', 'weight' => '66 kg',
                'complexion' => 'Medium Fair', 'blood_group' => 'A+',
                'division' => 'Chattogram', 'district' => 'Feni', 'present_address' => 'Feni Sadar',
                'grew_up' => 'Feni', 'area' => 'Sadar',
                'qualification' => 'HSC',
                'graduation_subject' => 'Business Studies', 'graduation_institution' => 'Feni Government College', 'graduation_year' => null,
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'General',
                'islamic_titles' => null, 'islamic_institution' => 'Maktab', 'islamic_year' => '2004',
                'occupation' => 'Business Owner', 'income' => 90000,
                'profession_details' => 'Owns a halal grocery and wholesale supply business.',
                'financial_status' => 'Upper Middle Class',
                'father_name' => 'Md Idris Miah', 'father_profession' => 'Retired businessman.',
                'mother_name' => 'Hasina Begum',
                'brothers' => 3, 'sisters' => 1,
                'partner_age' => '22-32', 'partner_height' => '5 ft 0 in+',
                'partner_education' => 'HSC or above, practicing',
                'partner_district' => 'Feni, Noakhali, Chattogram',
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Jannatul Mawa', 'email' => 'jannatul.mawa@heavenlymatch.test',
                'mobile' => '01710000012', 'guardian_mobile' => '01710001012',
                'profile_created_for' => 'sister', 'gender' => 'female',
                'birth_date' => '2002-01-09', 'height' => '5 ft 0 in', 'weight' => '48 kg',
                'complexion' => 'Medium', 'blood_group' => 'O+',
                'division' => 'Chattogram', 'district' => 'Noakhali', 'present_address' => 'Maijdee, Noakhali',
                'grew_up' => 'Noakhali', 'area' => 'Maijdee',
                'qualification' => 'Fazil Running',
                'graduation_subject' => 'Islamic Studies', 'graduation_institution' => 'Noakhali Alia Madrasa', 'graduation_year' => null,
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null,
                'education_method' => 'Alia',
                'islamic_titles' => 'Alima', 'islamic_institution' => 'Noakhali Alia Madrasa', 'islamic_year' => '2021',
                'occupation' => 'Student', 'income' => 0,
                'profession_details' => 'Studying in madrasa and assisting younger students.',
                'financial_status' => 'Middle Class',
                'father_name' => 'Maulana Sirajul Islam', 'father_profession' => 'Imam and madrasa teacher.',
                'mother_name' => 'Khadija Begum',
                'brothers' => 2, 'sisters' => 1,
                'partner_age' => '25-34', 'partner_height' => '5 ft 4 in+',
                'partner_education' => 'Madrasa educated or practicing graduate',
                'partner_district' => 'Noakhali, Feni, Lakshmipur',
                'approval_status' => 'approved',
            ],
        ];
    }
}
