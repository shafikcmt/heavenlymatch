<?php

declare(strict_types=1);

return [

    // ── Interest / connection ─────────────────────────────────────────────────
    'interest_received_title'   => ':name আপনাকে আগ্রহ পাঠিয়েছেন',
    'interest_received_body'    => ':name আপনার প্রোফাইলে আগ্রহী। তাদের বায়োডাটা দেখুন এবং সাড়া দিন।',
    'interest_accepted_title'   => ':name আপনার আগ্রহ গ্রহণ করেছেন',
    'interest_accepted_body'    => 'সুখবর! :name আপনার আগ্রহ গ্রহণ করেছেন। এখন আপনারা একে অপরকে বার্তা পাঠাতে পারবেন।',
    'interest_declined_title'   => ':name আপনার আগ্রহ প্রত্যাখ্যান করেছেন',
    'interest_declined_body'    => ':name আপনার আগ্রহ প্রত্যাখ্যান করেছেন।',
    'guardian_pending_title'    => 'অভিভাবকের অনুমোদনের অপেক্ষায়',
    'guardian_pending_body'     => ':name-এর অভিভাবকের অনুমোদনের জন্য আগ্রহের অনুরোধটি অপেক্ষায় রয়েছে।',

    // ── Photo access ──────────────────────────────────────────────────────────
    'photo_requested_title' => ':name আপনার ছবি দেখতে চেয়েছেন',
    'photo_requested_body'  => ':name আপনার ছবি দেখার অনুরোধ করেছেন। তাদের প্রোফাইল দেখুন এবং সিদ্ধান্ত নিন।',
    'photo_granted_title'   => ':name ছবি দেখার অনুমতি দিয়েছেন',
    'photo_granted_body'    => ':name আপনার ছবির অনুরোধ অনুমোদন করেছেন। তাদের প্রোফাইল পরিদর্শন করুন।',
    'photo_denied_title'    => 'ছবির অনুরোধ অনুমোদন হয়নি',
    'photo_denied_body'     => ':name আপনার ছবির অনুরোধ প্রত্যাখ্যান করেছেন।',

    // ── Messaging ─────────────────────────────────────────────────────────────
    'message_received_title' => ':name এর কাছ থেকে নতুন বার্তা',
    'message_received_body'  => ':name আপনাকে একটি বার্তা পাঠিয়েছেন।',

    // ── Membership / payment ──────────────────────────────────────────────────
    'payment_received_title'     => 'পেমেন্ট প্রাপ্ত হয়েছে',
    'payment_received_body'      => 'আমরা আপনার :plan পেমেন্ট পেয়েছি। এটি পর্যালোচনাধীন এবং ২৪ ঘণ্টার মধ্যে নিশ্চিত করা হবে।',
    'membership_activated_title' => ':plan সদস্যপদ সক্রিয় হয়েছে',
    'membership_activated_body'  => 'অভিনন্দন! আপনার :plan সদস্যপদ :date পর্যন্ত সক্রিয় হয়েছে।',
    'membership_rejected_title'  => 'পেমেন্ট যাচাই হয়নি',
    'membership_rejected_body'   => 'আমরা আপনার পেমেন্ট যাচাই করতে পারিনি। অনুগ্রহ করে সাপোর্টে যোগাযোগ করুন অথবা পুনরায় জমা দিন।',
    'membership_expiring_title'  => 'সদস্যপদ শীঘ্রই শেষ হবে',
    'membership_expiring_body'   => 'আপনার :plan সদস্যপদ :date তারিখে শেষ হবে। সংযুক্ত থাকতে এখনই নবায়ন করুন।',

    // ── Biodata / verification ────────────────────────────────────────────────
    'biodata_approved_title'  => 'বায়োডাটা অনুমোদিত হয়েছে',
    'biodata_approved_body'   => 'আপনার বায়োডাটা অনুমোদিত হয়েছে এবং এখন সদস্যদের কাছে দৃশ্যমান।',
    'biodata_rejected_title'  => 'বায়োডাটায় সংশোধন প্রয়োজন',
    'biodata_rejected_body'   => 'আপনার বায়োডাটা অনুমোদিত হয়নি। কারণ: :reason। অনুগ্রহ করে আপডেট করে পুনরায় জমা দিন।',
    'identity_verified_title' => 'পরিচয় যাচাই হয়েছে',
    'identity_verified_body'  => 'আপনার পরিচয় যাচাই হয়েছে। আপনার প্রোফাইলে এখন একটি যাচাইকৃত ব্যাজ দেখাবে।',

    // ── Profile views ─────────────────────────────────────────────────────────
    'profile_viewed_title' => 'কেউ আপনার প্রোফাইল দেখেছেন',
    'profile_viewed_body'  => 'আজ :count জন আপনার প্রোফাইল দেখেছেন।',

    // ── Admin / system ────────────────────────────────────────────────────────
    'account_warning_title'   => 'অ্যাকাউন্ট সতর্কবার্তা',
    'account_warning_body'    => 'আপনার অ্যাকাউন্টে একটি সতর্কবার্তা দেওয়া হয়েছে: :reason',
    'account_suspended_title' => 'অ্যাকাউন্ট সাসপেন্ড করা হয়েছে',
    'account_suspended_body'  => 'আপনার অ্যাকাউন্ট সাসপেন্ড করা হয়েছে। কারণ: :reason। আপিলের জন্য সাপোর্টে যোগাযোগ করুন।',

    // ── Email subjects ───────────────────────────────────────────────────────
    'email_subject_verify'     => 'আপনার HeavenlyMatch ইমেইল ঠিকানা যাচাই করুন',
    'email_subject_reset'      => 'আপনার HeavenlyMatch পাসওয়ার্ড রিসেট করুন',
    'email_subject_interest'   => ':name আপনাকে বিবাহের আগ্রহ পাঠিয়েছেন',
    'email_subject_accepted'   => 'আপনার আগ্রহ গৃহীত হয়েছে',
    'email_subject_membership' => 'আপনার HeavenlyMatch :plan সদস্যপদ সক্রিয়',
    'email_subject_biodata'    => 'আপনার বায়োডাটার অবস্থা আপডেট',
    'email_subject_daily'      => 'আপনার দৈনিক ম্যাচ পরামর্শ প্রস্তুত',

];
