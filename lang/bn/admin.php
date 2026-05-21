<?php

declare(strict_types=1);

return [

    // ── Dashboard ────────────────────────────────────────────────────────────
    'dashboard_title'    => 'অ্যাডমিন ড্যাশবোর্ড',
    'total_users'        => 'মোট ব্যবহারকারী',
    'active_users'       => 'সক্রিয় ব্যবহারকারী',
    'new_today'          => 'আজকের নতুন',
    'pending_review'     => 'পর্যালোচনার অপেক্ষায়',
    'total_premium'      => 'প্রিমিয়াম সদস্য',
    'pending_payments'   => 'বিচারাধীন পেমেন্ট',
    'open_reports'       => 'খোলা রিপোর্ট',
    'recent_users'       => 'সাম্প্রতিক নিবন্ধন',

    // ── User management ──────────────────────────────────────────────────────
    'users_title'          => 'ব্যবহারকারী ব্যবস্থাপনা',
    'user_details'         => 'ব্যবহারকারীর বিবরণ',
    'user_ban'             => 'ব্যান করুন',
    'user_ban_reason'      => 'ব্যানের কারণ',
    'user_unban'           => 'ব্যান তুলুন',
    'user_suspend'         => 'সাসপেন্ড করুন',
    'user_activate'        => 'সক্রিয় করুন',
    'user_verify_identity' => 'পরিচয় যাচাই করুন',
    'user_banned'          => 'ব্যবহারকারীকে ব্যান করা হয়েছে।',
    'user_verified'        => 'ব্যবহারকারীর পরিচয় যাচাই করা হয়েছে।',
    'filter_status'        => 'অবস্থা অনুযায়ী ফিল্টার',
    'filter_membership'    => 'সদস্যপদ অনুযায়ী ফিল্টার',
    'filter_gender'        => 'লিঙ্গ অনুযায়ী ফিল্টার',
    'filter_mode'          => 'মোড অনুযায়ী ফিল্টার',

    // ── Biodata review ────────────────────────────────────────────────────────
    'biodatas_title'   => 'বায়োডাটা পর্যালোচনা',
    'biodata_approve'  => 'বায়োডাটা অনুমোদন করুন',
    'biodata_reject'   => 'বায়োডাটা প্রত্যাখ্যান করুন',
    'rejection_note'   => 'প্রত্যাখ্যানের কারণ (আবশ্যক)',
    'approved_by'      => 'অনুমোদনকারী',
    'rejected_by'      => 'প্রত্যাখ্যানকারী',
    'approved_at'      => 'অনুমোদনের তারিখ',
    'biodata_approved' => 'বায়োডাটা অনুমোদিত হয়েছে।',
    'biodata_rejected' => 'বায়োডাটা প্রত্যাখ্যাত হয়েছে।',
    'no_pending'       => 'কোনো মুলতুবি বায়োডাটা নেই।',

    // ── Payments ─────────────────────────────────────────────────────────────
    'payments_title'      => 'পেমেন্ট অনুমোদন',
    'payment_approve'     => 'পেমেন্ট অনুমোদন করুন',
    'payment_reject'      => 'পেমেন্ট প্রত্যাখ্যান করুন',
    'payment_method'      => 'পেমেন্ট পদ্ধতি',
    'transaction_id'      => 'ট্রানজেকশন আইডি',
    'sender_number'       => 'প্রেরকের নম্বর',
    'amount'              => 'পরিমাণ (টাকা)',
    'plan_name'           => 'প্ল্যান',
    'submitted_at'        => 'জমা দেওয়ার তারিখ',
    'payment_approved'    => 'পেমেন্ট অনুমোদিত হয়েছে। সদস্যপদ সক্রিয় হয়েছে।',
    'payment_rejected'    => 'পেমেন্ট প্রত্যাখ্যাত হয়েছে।',
    'no_pending_payments' => 'কোনো মুলতুবি পেমেন্ট নেই।',

    // ── Reports ───────────────────────────────────────────────────────────────
    'reports_title'   => 'রিপোর্ট ও মডারেশন',
    'report_resolve'  => 'সমাধান করুন',
    'report_dismiss'  => 'বাতিল করুন',
    'resolution_note' => 'সমাধানের নোট',
    'reporter'        => 'রিপোর্টকারী',
    'reported_user'   => 'রিপোর্টকৃত ব্যবহারকারী',
    'report_reason'   => 'কারণ',
    'report_details'  => 'বিবরণ',
    'report_status'   => 'অবস্থা',
    'report_resolved' => 'রিপোর্ট সমাধান হয়েছে।',
    'no_open_reports' => 'কোনো খোলা রিপোর্ট নেই।',

    // ── Blog/CMS ──────────────────────────────────────────────────────────────
    'blog_title'       => 'ব্লগ ব্যবস্থাপনা',
    'post_create'      => 'নতুন পোস্ট',
    'post_edit'        => 'পোস্ট সম্পাদনা',
    'post_delete'      => 'পোস্ট মুছুন',
    'post_publish'     => 'প্রকাশ করুন',
    'post_unpublish'   => 'প্রকাশনা বাতিল করুন',
    'post_title'       => 'শিরোনাম',
    'post_content'     => 'বিষয়বস্তু',
    'post_category'    => 'বিভাগ',
    'post_tags'        => 'ট্যাগ',
    'meta_title'       => 'মেটা শিরোনাম',
    'meta_description' => 'মেটা বিবরণ',
    'post_published'   => 'পোস্ট প্রকাশিত হয়েছে।',
    'post_saved'       => 'পোস্ট সংরক্ষিত হয়েছে।',

    // ── Settings ─────────────────────────────────────────────────────────────
    'settings_title' => 'সিস্টেম সেটিংস',
    'setting_key'    => 'সেটিং',
    'setting_value'  => 'মান',
    'settings_saved' => 'সেটিংস সংরক্ষিত হয়েছে।',

    // ── Roles ─────────────────────────────────────────────────────────────────
    'role_super_admin' => 'সুপার অ্যাডমিন',
    'role_admin'       => 'অ্যাডমিন',
    'role_moderator'   => 'মডারেটর',
    'role_support'     => 'সাপোর্ট এজেন্ট',
    'role_finance'     => 'ফিনান্স ম্যানেজার',

    // ── Verification ─────────────────────────────────────────────────────────
    'identity_status_unverified'     => 'যাচাইবিহীন',
    'identity_status_pending_review' => 'পর্যালোচনাধীন',
    'identity_status_verified'       => 'যাচাইকৃত',
    'identity_status_rejected'       => 'প্রত্যাখ্যাত',

];
