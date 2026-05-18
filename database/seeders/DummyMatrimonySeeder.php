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
            $this->command?->warn('registrations or biodatas table not found. Run php artisan migrate first.');
            return;
        }

        foreach ($this->profiles() as $index => $profile) {
            $registrationId = 'HM9' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT);
            $email = $profile['email'];

            $registrationData = [
                'registration_id' => $registrationId,
                'profile_for' => $profile['profile_for'],
                'name' => $profile['name'],
                'gender' => $profile['gender'],
                'preferred_language' => 'bn',
                'email' => $email,
                'email_verification_code' => null,
                'email_verification_token' => null,
                'email_verification_sent_at' => now(),
                'is_email_verified' => true,
                'email_verified_at' => now(),
                'country_code' => '+880',
                'mobile_number' => $profile['mobile'],
                'mobile_verification_code' => null,
                'is_mobile_verified' => true,
                'status' => 'active',
                'password' => Hash::make($this->password),
                'terms_accepted_at' => now(),
                'last_login_at' => now()->subDays(rand(1, 20)),
            ];

            $user = Registration::where('email', $email)->first();

            if (! $user) {
                $user = Registration::create($this->onlyExistingColumns('registrations', $registrationData));
            } else {
                $updateData = $registrationData;
                unset($updateData['registration_id'], $updateData['email']);
                $user->fill($this->onlyExistingColumns('registrations', $updateData));
                $user->save();
            }

            $biodata = array_merge($this->baseBiodata($profile), [
                'registration_id' => $user->registration_id,
                'biodata_type' => $profile['biodata_type'],
                'birth_date' => $profile['birth_date'],
                'height' => $profile['height'],
                'weight' => $profile['weight'],
                'complexion' => $profile['complexion'],
                'blood_group' => $profile['blood_group'],
                'permanent_address' => $profile['district'],
                'present_address' => $profile['present_address'],
                'grew_up' => $profile['grew_up'],
                'highest_qualification' => $profile['qualification'],
                'graduation_subject' => $profile['subject'],
                'graduation_institution' => $profile['institution'],
                'occupation' => $profile['occupation'],
                'monthly_income' => $profile['income'],
                'partner_age' => $profile['partner_age'],
                'partner_height' => $profile['partner_height'],
                'partner_education' => $profile['partner_education'],
                'partner_district' => $profile['partner_district'],
                'groom_name' => $profile['name'],
                'guardian_mobile' => $profile['guardian_mobile'],
                'guardian_email' => 'guardian.' . Str::slug($profile['name'], '.') . '@example.com',
            ]);

            Biodata::updateOrCreate(
                ['registration_id' => $user->registration_id],
                $this->onlyExistingColumns('biodatas', $biodata)
            );
        }

        $this->command?->info('Dummy matrimony seed completed. Demo password for all accounts: ' . $this->password);
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
        $isGroom = $profile['biodata_type'] === 'groom';

        return [
            'marital_status' => 'Never Married',
            'previous_marriage_details' => null,
            'children_count' => 0,
            'nationality' => 'Bangladeshi',
            'village_area' => $profile['area'],

            'education_method' => $profile['education_method'],
            'other_education' => 'Basic Islamic education, regular Quran learning and selected online courses.',
            'ssc_year' => '2014',
            'ssc_group' => 'Science',
            'diploma_subject' => null,
            'diploma_medium' => null,
            'diploma_institution' => null,
            'diploma_year' => null,
            'graduation_year' => $profile['graduation_year'],
            'postgraduation_subject' => $profile['postgraduation_subject'],
            'postgraduation_institution' => $profile['postgraduation_institution'],
            'postgraduation_year' => $profile['postgraduation_year'],
            'islamic_titles' => $profile['islamic_titles'],
            'islamic_institution' => $profile['islamic_institution'],
            'islamic_year' => $profile['islamic_year'],

            'father_name' => $profile['father_name'],
            'father_alive' => 'Yes',
            'father_profession' => $profile['father_profession'],
            'mother_name' => $profile['mother_name'],
            'mother_alive' => 'Yes',
            'mother_profession' => 'Homemaker and active in family responsibilities.',
            'brothers' => $profile['brothers'],
            'brothers_info' => $profile['brothers'] > 0 ? 'Brothers are studying or working in halal professions.' : null,
            'sisters' => $profile['sisters'],
            'sisters_info' => $profile['sisters'] > 0 ? 'Sisters are studying or married in practicing families.' : null,
            'uncle_profession' => 'Service, business and farming among close relatives.',
            'family_financial_status' => $profile['financial_status'],
            'home_ownership' => 'Family owns a permanent home in native district and currently lives in a clean rented/owned residence according to work location.',
            'family_details' => 'Educated, respectful and marriage-focused family. They value deen, good manners and family responsibility.',
            'family_religious_condition' => 'Family members try to maintain prayers, modesty and halal earnings. They are supportive about Islamic marriage values.',

            'clothing_style' => $isGroom ? 'Modest Punjabi/shirt-pant outside, tries to maintain Islamic dress code.' : 'Borka and hijab outside, tries to maintain modest dressing with proper privacy.',
            'niqab_since' => $isGroom ? null : 'Practicing modest dress for several years; niqab preference depends on family environment.',
            'beard_info' => $isGroom ? 'Keeps beard and tries to follow sunnah according to ability.' : null,
            'clothes_above_ankles' => $isGroom ? 'Yes, tries to keep clothes above ankles.' : null,
            'prays_five_times' => 'Yes',
            'prayers_info' => 'Prays five times regularly and tries to improve concentration in salah.',
            'prayers_qaza_weekly' => 'Usually none, sometimes rare due to travel or illness.',
            'mahram_nonmahram' => 'Tries to maintain mahram/non-mahram boundaries respectfully.',
            'quran_recitation' => 'Can recite Quran and continues learning tajweed.',
            'fiqh' => 'Hanafi',
            'watch_entertainment' => 'Avoids harmful entertainment and tries to use time productively.',
            'diseases' => 'No major physical or mental disease known.',
            'religious_work' => 'Occasionally attends Islamic talks and local learning circles.',
            'beliefs_on_mazar' => 'Respects scholars but avoids any practice that conflicts with tawheed.',
            'books_read' => 'Ar-Raheeq Al-Makhtum, Riyadus Saliheen, Seerah related books.',
            'favorite_scholars' => 'Mufti Taqi Usmani, Dr. Abdullah Jahangir, local respected scholars.',
            'special_category' => null,
            'hobbies' => 'Reading, family time, learning new skills and helping others.',
            'groom_mobile' => $profile['mobile'],
            'groom_photo' => null,

            'profession_details' => $profile['profession_details'],
            'profession_halal_status' => 'Income source is halal and free from direct interest-based or unlawful work as much as possible.',

            'guardian_agree' => 'Yes',
            'wife_in_veil' => $isGroom ? 'Yes, prefers modest Islamic dress with comfort and family support.' : null,
            'wife_study_allowed' => $isGroom ? 'Yes, beneficial study can continue with mutual understanding.' : null,
            'wife_job_allowed' => $isGroom ? 'Discussable if environment is safe, halal and family-friendly.' : null,
            'residence_after_marriage' => 'Will be decided with family consultation; priority is a peaceful and Islamic environment.',
            'expect_gift_from_bride' => 'No',
            'marriage_plan' => 'Simple halal marriage with minimum unnecessary customs and family consent.',

            'partner_complexion' => 'Any, Fair, Bright',
            'partner_marital_status' => 'Never Married',
            'partner_profession' => 'Any halal profession or student with good character.',
            'partner_financial_condition' => 'Middle class or compatible family background.',
            'partner_expectations' => 'Practicing Muslim, honest, family-oriented, respectful and serious about marriage responsibilities.',

            'parents_know' => 'Yes',
            'truth_testify' => 'Yes',
            'responsibility' => 'Yes',
            'privacy_consent' => 'Yes',

            'guardian_relationship' => $isGroom ? 'Father' : 'Brother',
            'is_completed' => true,
            'completion_status' => 'completed',
            'approval_status' => $profile['approval_status'],
            'admin_note' => $profile['approval_status'] === 'pending' ? 'Dummy profile pending review.' : null,
        ];
    }

    private function profiles(): array
    {
        return [
            [
                'name' => 'Md Shafiqul Islam', 'email' => 'demo.groom@heavenlymatch.test', 'mobile' => '01710000001', 'guardian_mobile' => '01710001001',
                'profile_for' => 'self', 'gender' => 'male', 'biodata_type' => 'groom', 'birth_date' => '1996-04-18', 'height' => '5 ft 8 in', 'weight' => '70 kg', 'complexion' => 'Bright', 'blood_group' => 'B+',
                'district' => 'Dhaka', 'present_address' => 'Mirpur, Dhaka', 'grew_up' => 'Dhaka', 'area' => 'Mirpur 10', 'qualification' => 'BSc in Computer Science', 'subject' => 'Computer Science', 'institution' => 'Daffodil International University', 'graduation_year' => '2019',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General + Islamic', 'islamic_titles' => null, 'islamic_institution' => 'Local masjid maktab', 'islamic_year' => '2010',
                'occupation' => 'Software Engineer', 'income' => 85000, 'profession_details' => 'Works as a software engineer in a private IT company in Dhaka.', 'financial_status' => 'Upper Middle Class', 'father_name' => 'Md Abdul Karim', 'father_profession' => 'Retired government service holder.', 'mother_name' => 'Mst Ayesha Begum', 'brothers' => 1, 'sisters' => 1,
                'partner_age' => '20-26', 'partner_height' => '5 ft 0 in - 5 ft 6 in', 'partner_education' => 'HSC/Bachelor or Islamic education', 'partner_district' => 'Dhaka, Gazipur, Narayanganj', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Nusrat Jahan', 'email' => 'demo.bride@heavenlymatch.test', 'mobile' => '01710000002', 'guardian_mobile' => '01710001002',
                'profile_for' => 'daughter', 'gender' => 'female', 'biodata_type' => 'bride', 'birth_date' => '1999-08-12', 'height' => '5 ft 3 in', 'weight' => '55 kg', 'complexion' => 'Fair', 'blood_group' => 'O+',
                'district' => 'Chattogram', 'present_address' => 'Panchlaish, Chattogram', 'grew_up' => 'Chattogram', 'area' => 'Panchlaish', 'qualification' => 'BA in English', 'subject' => 'English', 'institution' => 'University of Chittagong', 'graduation_year' => '2021',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General + Islamic', 'islamic_titles' => null, 'islamic_institution' => 'Home Quran teacher', 'islamic_year' => '2012',
                'occupation' => 'Teacher', 'income' => 25000, 'profession_details' => 'Works as a teacher at a girls school and maintains modest environment.', 'financial_status' => 'Middle Class', 'father_name' => 'Mohammad Harun', 'father_profession' => 'Small business owner in halal clothing business.', 'mother_name' => 'Fatema Khatun', 'brothers' => 2, 'sisters' => 0,
                'partner_age' => '26-34', 'partner_height' => '5 ft 5 in+', 'partner_education' => 'Bachelor or above / Alim or above', 'partner_district' => 'Chattogram, Dhaka, Cumilla', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Abdullah Al Mamun', 'email' => 'abdullah.mamun@heavenlymatch.test', 'mobile' => '01710000003', 'guardian_mobile' => '01710001003',
                'profile_for' => 'self', 'gender' => 'male', 'biodata_type' => 'groom', 'birth_date' => '1993-01-20', 'height' => '5 ft 7 in', 'weight' => '68 kg', 'complexion' => 'Medium', 'blood_group' => 'A+',
                'district' => 'Rajshahi', 'present_address' => 'Shaheb Bazar, Rajshahi', 'grew_up' => 'Rajshahi', 'area' => 'Shaheb Bazar', 'qualification' => 'MBA', 'subject' => 'Management', 'institution' => 'University of Rajshahi', 'graduation_year' => '2016',
                'postgraduation_subject' => 'MBA', 'postgraduation_institution' => 'University of Rajshahi', 'postgraduation_year' => '2018', 'education_method' => 'General', 'islamic_titles' => null, 'islamic_institution' => 'Local madrasa evening course', 'islamic_year' => '2013',
                'occupation' => 'Bank Operations Officer', 'income' => 62000, 'profession_details' => 'Works in operations and is trying to move into a more halal-compliant finance role.', 'financial_status' => 'Middle Class', 'father_name' => 'Md Jalal Uddin', 'father_profession' => 'Owns a grocery wholesale business.', 'mother_name' => 'Rokeya Begum', 'brothers' => 1, 'sisters' => 2,
                'partner_age' => '22-29', 'partner_height' => '5 ft 0 in+', 'partner_education' => 'Graduate or madrasa educated', 'partner_district' => 'Rajshahi, Bogura, Naogaon', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Sumaiya Akter', 'email' => 'sumaiya.akter@heavenlymatch.test', 'mobile' => '01710000004', 'guardian_mobile' => '01710001004',
                'profile_for' => 'sister', 'gender' => 'female', 'biodata_type' => 'bride', 'birth_date' => '2001-03-08', 'height' => '5 ft 1 in', 'weight' => '49 kg', 'complexion' => 'Bright Fair', 'blood_group' => 'AB+',
                'district' => 'Sylhet', 'present_address' => 'Zindabazar, Sylhet', 'grew_up' => 'Sylhet', 'area' => 'Zindabazar', 'qualification' => 'HSC', 'subject' => 'Science', 'institution' => 'Sylhet Government College', 'graduation_year' => null,
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General + Islamic', 'islamic_titles' => 'Hafiza', 'islamic_institution' => 'Local Hifz Madrasa', 'islamic_year' => '2018',
                'occupation' => 'Student', 'income' => 0, 'profession_details' => 'Currently studying and helping family at home.', 'financial_status' => 'Middle Class', 'father_name' => 'Abdul Matin', 'father_profession' => 'Expatriate worker in Saudi Arabia.', 'mother_name' => 'Jannatul Ferdous', 'brothers' => 1, 'sisters' => 1,
                'partner_age' => '25-32', 'partner_height' => '5 ft 5 in+', 'partner_education' => 'Graduate / Alim / Qawmi', 'partner_district' => 'Sylhet, Dhaka, Moulvibazar', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Mahmudul Hasan', 'email' => 'mahmudul.hasan@heavenlymatch.test', 'mobile' => '01710000005', 'guardian_mobile' => '01710001005',
                'profile_for' => 'self', 'gender' => 'male', 'biodata_type' => 'groom', 'birth_date' => '1994-11-05', 'height' => '5 ft 10 in', 'weight' => '76 kg', 'complexion' => 'Fair', 'blood_group' => 'O-',
                'district' => 'Khulna', 'present_address' => 'Sonadanga, Khulna', 'grew_up' => 'Khulna', 'area' => 'Sonadanga', 'qualification' => 'Diploma Engineer', 'subject' => 'Civil Engineering', 'institution' => 'Khulna Polytechnic Institute', 'graduation_year' => '2015',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General', 'islamic_titles' => null, 'islamic_institution' => 'Masjid based classes', 'islamic_year' => '2009',
                'occupation' => 'Civil Engineer', 'income' => 70000, 'profession_details' => 'Works in private construction consultancy with halal income.', 'financial_status' => 'Upper Middle Class', 'father_name' => 'Md Abdur Rahman', 'father_profession' => 'Owns agricultural land and supervises farming.', 'mother_name' => 'Saleha Begum', 'brothers' => 0, 'sisters' => 2,
                'partner_age' => '20-27', 'partner_height' => '5 ft 1 in+', 'partner_education' => 'HSC or above', 'partner_district' => 'Khulna, Jashore, Satkhira', 'approval_status' => 'pending',
            ],
            [
                'name' => 'Ayesha Siddika', 'email' => 'ayesha.siddika@heavenlymatch.test', 'mobile' => '01710000006', 'guardian_mobile' => '01710001006',
                'profile_for' => 'daughter', 'gender' => 'female', 'biodata_type' => 'bride', 'birth_date' => '1998-06-25', 'height' => '5 ft 4 in', 'weight' => '58 kg', 'complexion' => 'Medium Fair', 'blood_group' => 'A-',
                'district' => 'Barishal', 'present_address' => 'Nathullabad, Barishal', 'grew_up' => 'Barishal', 'area' => 'Nathullabad', 'qualification' => 'BSS', 'subject' => 'Economics', 'institution' => 'University of Barishal', 'graduation_year' => '2020',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General + Islamic', 'islamic_titles' => null, 'islamic_institution' => 'Home Islamic study', 'islamic_year' => '2014',
                'occupation' => 'Homemaker', 'income' => 0, 'profession_details' => 'Prefers family life and learning useful skills from home.', 'financial_status' => 'Middle Class', 'father_name' => 'Md Shah Alam', 'father_profession' => 'Private service holder in a trading company.', 'mother_name' => 'Nasima Begum', 'brothers' => 1, 'sisters' => 1,
                'partner_age' => '27-35', 'partner_height' => '5 ft 5 in+', 'partner_education' => 'Graduate or stable halal profession', 'partner_district' => 'Barishal, Dhaka, Khulna', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Mizanur Rahman', 'email' => 'mizanur.rahman@heavenlymatch.test', 'mobile' => '01710000007', 'guardian_mobile' => '01710001007',
                'profile_for' => 'self', 'gender' => 'male', 'biodata_type' => 'groom', 'birth_date' => '1991-09-17', 'height' => '5 ft 6 in', 'weight' => '64 kg', 'complexion' => 'Medium', 'blood_group' => 'B-',
                'district' => 'Mymensingh', 'present_address' => 'Charpara, Mymensingh', 'grew_up' => 'Mymensingh', 'area' => 'Charpara', 'qualification' => 'Alim + BA', 'subject' => 'Islamic Studies', 'institution' => 'Ananda Mohan College', 'graduation_year' => '2015',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'Alia', 'islamic_titles' => 'Mawlana', 'islamic_institution' => 'Alia Madrasa', 'islamic_year' => '2011',
                'occupation' => 'Madrasa Teacher', 'income' => 38000, 'profession_details' => 'Teaches Islamic studies in a madrasa and tutors students.', 'financial_status' => 'Middle Class', 'father_name' => 'Md Nurul Islam', 'father_profession' => 'Retired school teacher.', 'mother_name' => 'Rahima Khatun', 'brothers' => 2, 'sisters' => 1,
                'partner_age' => '22-30', 'partner_height' => 'Any suitable height', 'partner_education' => 'Madrasa educated preferred', 'partner_district' => 'Mymensingh, Netrokona, Kishoreganj', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Farhana Tasnim', 'email' => 'farhana.tasnim@heavenlymatch.test', 'mobile' => '01710000008', 'guardian_mobile' => '01710001008',
                'profile_for' => 'relative', 'gender' => 'female', 'biodata_type' => 'bride', 'birth_date' => '2000-12-14', 'height' => '5 ft 2 in', 'weight' => '52 kg', 'complexion' => 'Fair', 'blood_group' => 'AB-',
                'district' => 'Cumilla', 'present_address' => 'Kandirpar, Cumilla', 'grew_up' => 'Cumilla', 'area' => 'Kandirpar', 'qualification' => 'BBA', 'subject' => 'Accounting', 'institution' => 'Cumilla Victoria College', 'graduation_year' => '2022',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General', 'islamic_titles' => null, 'islamic_institution' => 'Weekend Islamic course', 'islamic_year' => '2016',
                'occupation' => 'Student', 'income' => 0, 'profession_details' => 'Preparing for higher studies and learning Quran translation.', 'financial_status' => 'Middle Class', 'father_name' => 'Md Yusuf Ali', 'father_profession' => 'Hardware business owner.', 'mother_name' => 'Momena Begum', 'brothers' => 1, 'sisters' => 2,
                'partner_age' => '26-33', 'partner_height' => '5 ft 5 in+', 'partner_education' => 'BBA/MBA/Engineering or Islamic education', 'partner_district' => 'Cumilla, Dhaka, Feni', 'approval_status' => 'pending',
            ],
            [
                'name' => 'Tanvir Ahmed', 'email' => 'tanvir.ahmed@heavenlymatch.test', 'mobile' => '01710000009', 'guardian_mobile' => '01710001009',
                'profile_for' => 'self', 'gender' => 'male', 'biodata_type' => 'groom', 'birth_date' => '1997-02-03', 'height' => '5 ft 9 in', 'weight' => '73 kg', 'complexion' => 'Bright', 'blood_group' => 'O+',
                'district' => 'Narayanganj', 'present_address' => 'Fatullah, Narayanganj', 'grew_up' => 'Narayanganj', 'area' => 'Fatullah', 'qualification' => 'BSc Textile Engineering', 'subject' => 'Textile Engineering', 'institution' => 'BUTEX', 'graduation_year' => '2020',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General', 'islamic_titles' => null, 'islamic_institution' => 'Local masjid halaqa', 'islamic_year' => '2011',
                'occupation' => 'Textile Engineer', 'income' => 78000, 'profession_details' => 'Works in a garments manufacturing company with compliance department.', 'financial_status' => 'Upper Middle Class', 'father_name' => 'Md Habibur Rahman', 'father_profession' => 'Transport business owner.', 'mother_name' => 'Shamsun Nahar', 'brothers' => 1, 'sisters' => 0,
                'partner_age' => '21-27', 'partner_height' => '5 ft 1 in+', 'partner_education' => 'Graduate preferred', 'partner_district' => 'Narayanganj, Dhaka, Munshiganj', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Mariya Islam', 'email' => 'mariya.islam@heavenlymatch.test', 'mobile' => '01710000010', 'guardian_mobile' => '01710001010',
                'profile_for' => 'daughter', 'gender' => 'female', 'biodata_type' => 'bride', 'birth_date' => '1997-07-07', 'height' => '5 ft 5 in', 'weight' => '60 kg', 'complexion' => 'Bright Fair', 'blood_group' => 'B+',
                'district' => 'Bogura', 'present_address' => 'Jaleshwaritola, Bogura', 'grew_up' => 'Bogura', 'area' => 'Jaleshwaritola', 'qualification' => 'MBBS', 'subject' => 'Medicine', 'institution' => 'Shaheed Ziaur Rahman Medical College', 'graduation_year' => '2022',
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General + Islamic', 'islamic_titles' => null, 'islamic_institution' => 'Online Islamic course', 'islamic_year' => '2017',
                'occupation' => 'Doctor', 'income' => 45000, 'profession_details' => 'Intern doctor, plans to work in a women-friendly healthcare environment.', 'financial_status' => 'Upper Middle Class', 'father_name' => 'Dr. Mahbub Alam', 'father_profession' => 'Senior doctor in a private hospital.', 'mother_name' => 'Sharmin Akter', 'brothers' => 0, 'sisters' => 1,
                'partner_age' => '28-36', 'partner_height' => '5 ft 6 in+', 'partner_education' => 'Doctor/Engineer/Graduate practicing Muslim', 'partner_district' => 'Bogura, Dhaka, Rajshahi', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Rafiqul Islam', 'email' => 'rafiqul.islam@heavenlymatch.test', 'mobile' => '01710000011', 'guardian_mobile' => '01710001011',
                'profile_for' => 'self', 'gender' => 'male', 'biodata_type' => 'groom', 'birth_date' => '1989-10-28', 'height' => '5 ft 6 in', 'weight' => '66 kg', 'complexion' => 'Medium Fair', 'blood_group' => 'A+',
                'district' => 'Feni', 'present_address' => 'Feni Sadar', 'grew_up' => 'Feni', 'area' => 'Sadar', 'qualification' => 'HSC', 'subject' => 'Business Studies', 'institution' => 'Feni Government College', 'graduation_year' => null,
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'General', 'islamic_titles' => null, 'islamic_institution' => 'Maktab', 'islamic_year' => '2004',
                'occupation' => 'Business Owner', 'income' => 90000, 'profession_details' => 'Owns a halal grocery and wholesale supply business.', 'financial_status' => 'Upper Middle Class', 'father_name' => 'Md Idris Miah', 'father_profession' => 'Retired businessman.', 'mother_name' => 'Hasina Begum', 'brothers' => 3, 'sisters' => 1,
                'partner_age' => '22-32', 'partner_height' => 'Any suitable height', 'partner_education' => 'HSC or above, practicing', 'partner_district' => 'Feni, Noakhali, Chattogram', 'approval_status' => 'approved',
            ],
            [
                'name' => 'Jannatul Mawa', 'email' => 'jannatul.mawa@heavenlymatch.test', 'mobile' => '01710000012', 'guardian_mobile' => '01710001012',
                'profile_for' => 'sister', 'gender' => 'female', 'biodata_type' => 'bride', 'birth_date' => '2002-01-09', 'height' => '5 ft 0 in', 'weight' => '48 kg', 'complexion' => 'Medium', 'blood_group' => 'O+',
                'district' => 'Noakhali', 'present_address' => 'Maijdee, Noakhali', 'grew_up' => 'Noakhali', 'area' => 'Maijdee', 'qualification' => 'Fazil Running', 'subject' => 'Islamic Studies', 'institution' => 'Noakhali Alia Madrasa', 'graduation_year' => null,
                'postgraduation_subject' => null, 'postgraduation_institution' => null, 'postgraduation_year' => null, 'education_method' => 'Alia', 'islamic_titles' => 'Alima', 'islamic_institution' => 'Noakhali Alia Madrasa', 'islamic_year' => '2021',
                'occupation' => 'Student', 'income' => 0, 'profession_details' => 'Studying in madrasa and assisting younger students.', 'financial_status' => 'Middle Class', 'father_name' => 'Maulana Sirajul Islam', 'father_profession' => 'Imam and madrasa teacher.', 'mother_name' => 'Khadija Begum', 'brothers' => 2, 'sisters' => 1,
                'partner_age' => '25-34', 'partner_height' => '5 ft 4 in+', 'partner_education' => 'Madrasa educated or practicing graduate', 'partner_district' => 'Noakhali, Feni, Lakshmipur', 'approval_status' => 'approved',
            ],
        ];
    }
}
