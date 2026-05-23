-- CreateTable
CREATE TABLE `countries` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `namebn` VARCHAR(100) NULL,
    `iso2` VARCHAR(2) NOT NULL,
    `iso3` VARCHAR(3) NOT NULL,
    `dialCode` VARCHAR(10) NULL,
    `isPopular` BOOLEAN NOT NULL DEFAULT false,
    `sortOrder` INTEGER NOT NULL DEFAULT 0,

    UNIQUE INDEX `countries_name_key`(`name`),
    UNIQUE INDEX `countries_iso2_key`(`iso2`),
    UNIQUE INDEX `countries_iso3_key`(`iso3`),
    INDEX `countries_isPopular_sortOrder_idx`(`isPopular`, `sortOrder`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `divisions` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(60) NOT NULL,
    `namebn` VARCHAR(60) NULL,
    `countryId` INTEGER NOT NULL,

    UNIQUE INDEX `divisions_countryId_name_key`(`countryId`, `name`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `districts` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(60) NOT NULL,
    `namebn` VARCHAR(60) NULL,
    `divisionId` INTEGER NOT NULL,

    INDEX `districts_divisionId_idx`(`divisionId`),
    UNIQUE INDEX `districts_divisionId_name_key`(`divisionId`, `name`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `upazilas` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(80) NOT NULL,
    `namebn` VARCHAR(80) NULL,
    `districtId` INTEGER NOT NULL,

    UNIQUE INDEX `upazilas_districtId_name_key`(`districtId`, `name`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `users` (
    `id` VARCHAR(191) NOT NULL,
    `registrationId` VARCHAR(12) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `emailVerifiedAt` DATETIME(3) NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `mobileVerifiedAt` DATETIME(3) NULL,
    `countryCode` VARCHAR(10) NOT NULL DEFAULT '+880',
    `passwordHash` VARCHAR(255) NOT NULL,
    `platformMode` ENUM('GENERAL', 'ISLAMIC') NOT NULL DEFAULT 'GENERAL',
    `accountStatus` ENUM('ACTIVE', 'INACTIVE', 'SUSPENDED', 'DELETED', 'PENDING_VERIFICATION') NOT NULL DEFAULT 'PENDING_VERIFICATION',
    `role` ENUM('SUPER_ADMIN', 'ADMIN', 'MODERATOR', 'SUPPORT_AGENT', 'FINANCE_MANAGER') NULL,
    `preferredLanguage` VARCHAR(5) NOT NULL DEFAULT 'bn',
    `termsAcceptedAt` DATETIME(3) NULL,
    `lastLoginAt` DATETIME(3) NULL,
    `lastActiveAt` DATETIME(3) NULL,
    `twoFactorEnabled` BOOLEAN NOT NULL DEFAULT false,
    `twoFactorSecret` VARCHAR(64) NULL,
    `deactivatedAt` DATETIME(3) NULL,
    `deletionRequestedAt` DATETIME(3) NULL,
    `blockedAt` DATETIME(3) NULL,
    `blockedReason` TEXT NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `users_registrationId_key`(`registrationId`),
    UNIQUE INDEX `users_email_key`(`email`),
    UNIQUE INDEX `users_mobile_key`(`mobile`),
    INDEX `users_accountStatus_platformMode_idx`(`accountStatus`, `platformMode`),
    INDEX `users_registrationId_idx`(`registrationId`),
    INDEX `users_lastActiveAt_idx`(`lastActiveAt`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `oauth_accounts` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `provider` VARCHAR(20) NOT NULL,
    `providerUid` VARCHAR(255) NOT NULL,
    `accessToken` TEXT NULL,
    `refreshToken` TEXT NULL,
    `expiresAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    UNIQUE INDEX `oauth_accounts_provider_providerUid_key`(`provider`, `providerUid`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `refresh_tokens` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `tokenHash` VARCHAR(64) NOT NULL,
    `deviceInfo` VARCHAR(255) NULL,
    `ipAddress` VARCHAR(45) NULL,
    `expiresAt` DATETIME(3) NOT NULL,
    `revokedAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    UNIQUE INDEX `refresh_tokens_tokenHash_key`(`tokenHash`),
    INDEX `refresh_tokens_userId_expiresAt_idx`(`userId`, `expiresAt`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `otp_codes` (
    `id` VARCHAR(191) NOT NULL,
    `target` VARCHAR(255) NOT NULL,
    `type` VARCHAR(30) NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `expiresAt` DATETIME(3) NOT NULL,
    `usedAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `otp_codes_target_type_expiresAt_idx`(`target`, `type`, `expiresAt`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `profiles` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `displayName` VARCHAR(50) NULL,
    `gender` ENUM('MALE', 'FEMALE') NOT NULL,
    `profileCreatedFor` VARCHAR(30) NOT NULL,
    `dateOfBirth` DATE NOT NULL,
    `maritalStatus` ENUM('NEVER_MARRIED', 'DIVORCED', 'WIDOWED', 'SEPARATED') NOT NULL DEFAULT 'NEVER_MARRIED',
    `numberOfChildren` INTEGER NOT NULL DEFAULT 0,
    `heightCm` INTEGER NULL,
    `weightKg` INTEGER NULL,
    `complexion` VARCHAR(30) NULL,
    `bloodGroup` VARCHAR(5) NULL,
    `hasDisability` BOOLEAN NOT NULL DEFAULT false,
    `disabilityDescription` TEXT NULL,
    `religion` ENUM('ISLAM', 'HINDUISM', 'CHRISTIANITY', 'BUDDHISM', 'OTHER') NOT NULL DEFAULT 'ISLAM',
    `sect` VARCHAR(50) NULL,
    `isPracticing` BOOLEAN NOT NULL DEFAULT true,
    `prayerHabits` VARCHAR(50) NULL,
    `prayerInMasjid` VARCHAR(20) NULL,
    `quranRecitation` VARCHAR(30) NULL,
    `islamicEducation` VARCHAR(100) NULL,
    `hasHajj` BOOLEAN NOT NULL DEFAULT false,
    `fiqhFollowed` VARCHAR(30) NULL,
    `beliefOnMazar` VARCHAR(50) NULL,
    `watchEntertainment` VARCHAR(30) NULL,
    `wearsHijab` VARCHAR(20) NULL,
    `wearsBurqa` VARCHAR(20) NULL,
    `wearNiqab` BOOLEAN NOT NULL DEFAULT false,
    `niqabSince` VARCHAR(30) NULL,
    `hasBeard` VARCHAR(20) NULL,
    `beardSince` VARCHAR(30) NULL,
    `clothesAboveAnkles` BOOLEAN NOT NULL DEFAULT false,
    `observesMahramRules` BOOLEAN NOT NULL DEFAULT false,
    `educationMethod` ENUM('GENERAL', 'ISLAMIC', 'BOTH') NOT NULL DEFAULT 'GENERAL',
    `highestQualification` ENUM('PRIMARY', 'JSC', 'SSC', 'HSC', 'DIPLOMA', 'GRADUATION', 'POST_GRADUATION', 'PHD', 'HAFEZ', 'ALIM', 'FAZIL', 'KAMIL') NULL,
    `educationDetails` JSON NULL,
    `islamicTitles` VARCHAR(200) NULL,
    `islamicInstitution` VARCHAR(200) NULL,
    `occupation` VARCHAR(80) NULL,
    `occupationCategory` VARCHAR(50) NULL,
    `company` VARCHAR(150) NULL,
    `monthlyIncome` INTEGER NULL,
    `currency` VARCHAR(5) NOT NULL DEFAULT 'BDT',
    `professionDetails` TEXT NULL,
    `fatherName` VARCHAR(100) NULL,
    `fatherAlive` BOOLEAN NULL,
    `fatherProfession` VARCHAR(100) NULL,
    `motherName` VARCHAR(100) NULL,
    `motherAlive` BOOLEAN NULL,
    `motherProfession` VARCHAR(100) NULL,
    `brothersCount` INTEGER NOT NULL DEFAULT 0,
    `sistersCount` INTEGER NOT NULL DEFAULT 0,
    `familyType` ENUM('JOINT', 'NUCLEAR', 'FLEXIBLE') NOT NULL DEFAULT 'NUCLEAR',
    `familyFinancialStatus` VARCHAR(30) NULL,
    `familyReligiousCondition` TEXT NULL,
    `familyDetails` TEXT NULL,
    `diet` VARCHAR(30) NULL,
    `smoking` VARCHAR(20) NULL,
    `wantsChildren` VARCHAR(20) NULL,
    `hasChildren` VARCHAR(20) NULL,
    `hobbies` TEXT NULL,
    `booksRead` TEXT NULL,
    `nationality` VARCHAR(60) NOT NULL DEFAULT 'Bangladeshi',
    `residingCountryId` INTEGER NULL,
    `residingCity` VARCHAR(80) NULL,
    `visaStatus` VARCHAR(30) NULL,
    `permanentAddress` TEXT NULL,
    `presentAddress` TEXT NULL,
    `grewUp` VARCHAR(100) NULL,
    `homeDivisionId` INTEGER NULL,
    `homeDistrictId` INTEGER NULL,
    `homeUpazilaId` INTEGER NULL,
    `guardianAgrees` BOOLEAN NULL,
    `expectedMarriageTime` VARCHAR(30) NULL,
    `wifeInVeil` VARCHAR(20) NULL,
    `wifeStudyAllowed` VARCHAR(20) NULL,
    `wifeJobAllowed` VARCHAR(20) NULL,
    `residenceAfterMarriage` VARCHAR(80) NULL,
    `expectGiftFromBride` VARCHAR(20) NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
    `adminNote` TEXT NULL,
    `isCompleted` BOOLEAN NOT NULL DEFAULT false,
    `isFeatured` BOOLEAN NOT NULL DEFAULT false,
    `profileScore` INTEGER NOT NULL DEFAULT 0,
    `completenessScore` INTEGER NOT NULL DEFAULT 0,
    `approvedAt` DATETIME(3) NULL,
    `approvedBy` VARCHAR(20) NULL,
    `rejectedAt` DATETIME(3) NULL,
    `rejectedBy` VARCHAR(20) NULL,
    `featuredAt` DATETIME(3) NULL,
    `photoVisibility` ENUM('PUBLIC', 'MEMBERS_ONLY', 'BLURRED') NOT NULL DEFAULT 'MEMBERS_ONLY',
    `parentsKnow` BOOLEAN NOT NULL DEFAULT false,
    `truthTestified` BOOLEAN NOT NULL DEFAULT false,
    `responsibilityAccepted` BOOLEAN NOT NULL DEFAULT false,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `profiles_userId_key`(`userId`),
    INDEX `profiles_gender_status_isCompleted_idx`(`gender`, `status`, `isCompleted`),
    INDEX `profiles_religion_sect_idx`(`religion`, `sect`),
    INDEX `profiles_occupation_monthlyIncome_idx`(`occupation`, `monthlyIncome`),
    INDEX `profiles_homeDistrictId_homeDivisionId_idx`(`homeDistrictId`, `homeDivisionId`),
    INDEX `profiles_residingCountryId_idx`(`residingCountryId`),
    INDEX `profiles_maritalStatus_heightCm_idx`(`maritalStatus`, `heightCm`),
    INDEX `profiles_completenessScore_status_idx`(`completenessScore`, `status`),
    INDEX `profiles_isFeatured_status_idx`(`isFeatured`, `status`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `photos` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `s3Key` VARCHAR(500) NOT NULL,
    `thumbnailKey` VARCHAR(500) NULL,
    `isPrimary` BOOLEAN NOT NULL DEFAULT false,
    `visibility` ENUM('PUBLIC', 'MEMBERS_ONLY', 'BLURRED') NOT NULL DEFAULT 'MEMBERS_ONLY',
    `isApproved` BOOLEAN NOT NULL DEFAULT false,
    `sortOrder` INTEGER NOT NULL DEFAULT 0,
    `uploadedAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `photos_profileId_isPrimary_idx`(`profileId`, `isPrimary`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `preferences` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `partnerAgeMin` INTEGER NULL,
    `partnerAgeMax` INTEGER NULL,
    `partnerHeightCmMin` INTEGER NULL,
    `partnerHeightCmMax` INTEGER NULL,
    `partnerComplexion` VARCHAR(30) NULL,
    `partnerReligion` ENUM('ISLAM', 'HINDUISM', 'CHRISTIANITY', 'BUDDHISM', 'OTHER') NULL,
    `partnerSect` VARCHAR(50) NULL,
    `partnerMustBePracticing` BOOLEAN NOT NULL DEFAULT false,
    `partnerEducation` ENUM('PRIMARY', 'JSC', 'SSC', 'HSC', 'DIPLOMA', 'GRADUATION', 'POST_GRADUATION', 'PHD', 'HAFEZ', 'ALIM', 'FAZIL', 'KAMIL') NULL,
    `partnerEducationMethod` ENUM('GENERAL', 'ISLAMIC', 'BOTH') NULL,
    `partnerNationality` VARCHAR(60) NULL,
    `partnerResidingCountryId` INTEGER NULL,
    `preferredDivisionId` INTEGER NULL,
    `preferredDistrictId` INTEGER NULL,
    `openToNRB` BOOLEAN NOT NULL DEFAULT true,
    `partnerOccupation` VARCHAR(80) NULL,
    `partnerIncomeMin` INTEGER NULL,
    `partnerIncomeMax` INTEGER NULL,
    `acceptNeverMarried` BOOLEAN NOT NULL DEFAULT true,
    `acceptDivorced` BOOLEAN NOT NULL DEFAULT false,
    `acceptWidowed` BOOLEAN NOT NULL DEFAULT false,
    `partnerFamilyType` ENUM('JOINT', 'NUCLEAR', 'FLEXIBLE') NULL,
    `partnerFinancialStatus` VARCHAR(30) NULL,
    `partnerMaritalStatus` ENUM('NEVER_MARRIED', 'DIVORCED', 'WIDOWED', 'SEPARATED') NULL,
    `partnerSmokingAllowed` BOOLEAN NOT NULL DEFAULT false,
    `expectations` TEXT NULL,
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `preferences_profileId_key`(`profileId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `verification_requests` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NULL,
    `type` VARCHAR(30) NOT NULL,
    `status` ENUM('UNVERIFIED', 'PENDING_REVIEW', 'VERIFIED', 'REJECTED') NOT NULL DEFAULT 'UNVERIFIED',
    `documentFrontKey` VARCHAR(500) NULL,
    `documentBackKey` VARCHAR(500) NULL,
    `selfieKey` VARCHAR(500) NULL,
    `documentNumber` VARCHAR(30) NULL,
    `reviewedBy` VARCHAR(20) NULL,
    `reviewedAt` DATETIME(3) NULL,
    `rejectionReason` TEXT NULL,
    `adminNote` TEXT NULL,
    `submittedAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    INDEX `verification_requests_userId_type_status_idx`(`userId`, `type`, `status`),
    INDEX `verification_requests_status_submittedAt_idx`(`status`, `submittedAt`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `interests` (
    `id` VARCHAR(191) NOT NULL,
    `senderId` VARCHAR(191) NOT NULL,
    `receiverId` VARCHAR(191) NOT NULL,
    `status` ENUM('PENDING', 'ACCEPTED', 'DECLINED', 'GUARDIAN_PENDING', 'WITHDRAWN', 'EXPIRED') NOT NULL DEFAULT 'PENDING',
    `message` TEXT NULL,
    `guardianNotified` BOOLEAN NOT NULL DEFAULT false,
    `guardianNotifiedAt` DATETIME(3) NULL,
    `respondedAt` DATETIME(3) NULL,
    `expiresAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    INDEX `interests_receiverId_status_idx`(`receiverId`, `status`),
    INDEX `interests_senderId_status_idx`(`senderId`, `status`),
    INDEX `interests_status_expiresAt_idx`(`status`, `expiresAt`),
    UNIQUE INDEX `interests_senderId_receiverId_key`(`senderId`, `receiverId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `conversations` (
    `id` VARCHAR(191) NOT NULL,
    `interestId` VARCHAR(191) NOT NULL,
    `isActive` BOOLEAN NOT NULL DEFAULT true,
    `lastMessageAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `conversations_interestId_key`(`interestId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `conversation_participants` (
    `id` VARCHAR(191) NOT NULL,
    `conversationId` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `lastReadAt` DATETIME(3) NULL,
    `isArchived` BOOLEAN NOT NULL DEFAULT false,
    `mutedUntil` DATETIME(3) NULL,

    UNIQUE INDEX `conversation_participants_conversationId_userId_key`(`conversationId`, `userId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `messages` (
    `id` VARCHAR(191) NOT NULL,
    `conversationId` VARCHAR(191) NOT NULL,
    `senderId` VARCHAR(191) NOT NULL,
    `body` TEXT NOT NULL,
    `type` VARCHAR(20) NOT NULL DEFAULT 'text',
    `attachmentKey` VARCHAR(500) NULL,
    `readAt` DATETIME(3) NULL,
    `deletedForSender` BOOLEAN NOT NULL DEFAULT false,
    `deletedForAll` BOOLEAN NOT NULL DEFAULT false,
    `flagged` BOOLEAN NOT NULL DEFAULT false,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `messages_conversationId_createdAt_idx`(`conversationId`, `createdAt`),
    INDEX `messages_senderId_idx`(`senderId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `guardians` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `relationship` VARCHAR(50) NOT NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NULL,
    `notificationLevel` VARCHAR(30) NOT NULL DEFAULT 'interests_only',
    `isVerified` BOOLEAN NOT NULL DEFAULT false,
    `verifiedAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `guardians_userId_key`(`userId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `photo_access_requests` (
    `id` VARCHAR(191) NOT NULL,
    `requesterId` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `profileOwnerId` VARCHAR(191) NOT NULL,
    `status` ENUM('PENDING', 'GRANTED', 'DENIED') NOT NULL DEFAULT 'PENDING',
    `respondedAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `photo_access_requests_profileOwnerId_status_idx`(`profileOwnerId`, `status`),
    UNIQUE INDEX `photo_access_requests_requesterId_profileId_key`(`requesterId`, `profileId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `shortlists` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `shortlistedId` VARCHAR(191) NOT NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `shortlists_userId_idx`(`userId`),
    UNIQUE INDEX `shortlists_userId_shortlistedId_key`(`userId`, `shortlistedId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `profile_views` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `viewerId` VARCHAR(191) NULL,
    `ipAddress` VARCHAR(45) NULL,
    `userAgent` VARCHAR(255) NULL,
    `viewedAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `profile_views_profileId_viewedAt_idx`(`profileId`, `viewedAt`),
    INDEX `profile_views_viewerId_profileId_idx`(`viewerId`, `profileId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `match_scores` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `candidateId` VARCHAR(191) NOT NULL,
    `totalScore` INTEGER NOT NULL,
    `scoreBreakdown` JSON NOT NULL,
    `computedAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `match_scores_userId_totalScore_idx`(`userId`, `totalScore`),
    UNIQUE INDEX `match_scores_userId_candidateId_key`(`userId`, `candidateId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `subscription_plans` (
    `id` VARCHAR(191) NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL,
    `tier` ENUM('FREE', 'SILVER', 'GOLD', 'DIAMOND') NOT NULL,
    `durationMonths` INTEGER NOT NULL,
    `priceLocal` DECIMAL(12, 2) NOT NULL,
    `priceIntl` DECIMAL(12, 2) NOT NULL,
    `currencyLocal` VARCHAR(5) NOT NULL DEFAULT 'BDT',
    `currencyIntl` VARCHAR(5) NOT NULL DEFAULT 'USD',
    `features` JSON NOT NULL,
    `limits` JSON NOT NULL,
    `badge` VARCHAR(30) NULL,
    `isPopular` BOOLEAN NOT NULL DEFAULT false,
    `isActive` BOOLEAN NOT NULL DEFAULT true,
    `sortOrder` INTEGER NOT NULL DEFAULT 0,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `subscription_plans_slug_key`(`slug`),
    INDEX `subscription_plans_tier_isActive_sortOrder_idx`(`tier`, `isActive`, `sortOrder`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `subscriptions` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `planId` VARCHAR(191) NOT NULL,
    `status` ENUM('ACTIVE', 'EXPIRED', 'CANCELLED', 'TRIAL') NOT NULL DEFAULT 'ACTIVE',
    `startedAt` DATETIME(3) NOT NULL,
    `expiresAt` DATETIME(3) NOT NULL,
    `cancelledAt` DATETIME(3) NULL,
    `autoRenew` BOOLEAN NOT NULL DEFAULT false,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `subscriptions_userId_key`(`userId`),
    INDEX `subscriptions_status_expiresAt_idx`(`status`, `expiresAt`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `payments` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `planId` VARCHAR(191) NULL,
    `amount` DECIMAL(12, 2) NOT NULL,
    `currency` VARCHAR(5) NOT NULL,
    `gateway` ENUM('SSLCOMMERZ', 'BKASH', 'NAGAD', 'STRIPE', 'PAYPAL', 'MANUAL') NOT NULL,
    `gatewayTxId` VARCHAR(200) NULL,
    `internalTxId` VARCHAR(30) NOT NULL,
    `status` ENUM('PENDING', 'PROCESSING', 'COMPLETED', 'FAILED', 'REFUNDED', 'CANCELLED', 'MANUAL_PENDING') NOT NULL DEFAULT 'PENDING',
    `purposeType` VARCHAR(30) NOT NULL,
    `purposeId` VARCHAR(50) NULL,
    `gatewayResponse` JSON NULL,
    `paidAt` DATETIME(3) NULL,
    `expiresAt` DATETIME(3) NULL,
    `refundedAt` DATETIME(3) NULL,
    `refundAmount` DECIMAL(12, 2) NULL,
    `ipAddress` VARCHAR(45) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `payments_internalTxId_key`(`internalTxId`),
    INDEX `payments_userId_status_idx`(`userId`, `status`),
    INDEX `payments_status_expiresAt_idx`(`status`, `expiresAt`),
    INDEX `payments_gateway_gatewayTxId_idx`(`gateway`, `gatewayTxId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `contact_unlocks` (
    `id` VARCHAR(191) NOT NULL,
    `unlockerId` VARCHAR(191) NOT NULL,
    `unlockedId` VARCHAR(191) NOT NULL,
    `paymentId` VARCHAR(191) NULL,
    `expiresAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `contact_unlocks_unlockerId_idx`(`unlockerId`),
    UNIQUE INDEX `contact_unlocks_unlockerId_unlockedId_key`(`unlockerId`, `unlockedId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `profile_boosts` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `paymentId` VARCHAR(191) NULL,
    `durationHours` INTEGER NOT NULL DEFAULT 24,
    `startedAt` DATETIME(3) NULL,
    `expiresAt` DATETIME(3) NULL,
    `isActive` BOOLEAN NOT NULL DEFAULT false,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `profile_boosts_userId_isActive_expiresAt_idx`(`userId`, `isActive`, `expiresAt`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `notifications` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `type` ENUM('NEW_INTEREST', 'INTEREST_ACCEPTED', 'INTEREST_DECLINED', 'NEW_MESSAGE', 'PHOTO_ACCESS_REQUEST', 'PHOTO_ACCESS_GRANTED', 'NEW_MATCH', 'PROFILE_VIEW', 'SUBSCRIPTION_EXPIRING', 'SUBSCRIPTION_EXPIRED', 'PAYMENT_SUCCESS', 'PAYMENT_FAILED', 'VERIFICATION_APPROVED', 'VERIFICATION_REJECTED', 'GUARDIAN_NOTIFICATION', 'ADMIN_MESSAGE', 'SYSTEM') NOT NULL,
    `channel` ENUM('IN_APP', 'EMAIL', 'SMS', 'PUSH') NOT NULL DEFAULT 'IN_APP',
    `title` VARCHAR(200) NOT NULL,
    `body` TEXT NOT NULL,
    `data` JSON NULL,
    `readAt` DATETIME(3) NULL,
    `sentAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `notifications_userId_readAt_idx`(`userId`, `readAt`),
    INDEX `notifications_userId_type_createdAt_idx`(`userId`, `type`, `createdAt`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `reports` (
    `id` VARCHAR(191) NOT NULL,
    `reporterId` VARCHAR(191) NOT NULL,
    `reportedId` VARCHAR(191) NOT NULL,
    `reason` ENUM('FAKE_PROFILE', 'INAPPROPRIATE_CONTENT', 'HARASSMENT', 'SPAM', 'SCAM', 'UNDERAGE', 'OTHER') NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('PENDING', 'UNDER_REVIEW', 'RESOLVED', 'DISMISSED') NOT NULL DEFAULT 'PENDING',
    `reviewedBy` VARCHAR(20) NULL,
    `reviewedAt` DATETIME(3) NULL,
    `resolution` TEXT NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    INDEX `reports_reportedId_status_idx`(`reportedId`, `status`),
    INDEX `reports_status_createdAt_idx`(`status`, `createdAt`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `blocks` (
    `id` VARCHAR(191) NOT NULL,
    `blockerId` VARCHAR(191) NOT NULL,
    `blockedId` VARCHAR(191) NOT NULL,
    `reason` VARCHAR(200) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `blocks_blockerId_idx`(`blockerId`),
    UNIQUE INDEX `blocks_blockerId_blockedId_key`(`blockerId`, `blockedId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `admin_logs` (
    `id` VARCHAR(191) NOT NULL,
    `adminId` VARCHAR(191) NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `targetType` VARCHAR(50) NULL,
    `targetId` VARCHAR(50) NULL,
    `metadata` JSON NULL,
    `ipAddress` VARCHAR(45) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `admin_logs_adminId_createdAt_idx`(`adminId`, `createdAt`),
    INDEX `admin_logs_targetType_targetId_idx`(`targetType`, `targetId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `blog_categories` (
    `id` VARCHAR(191) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    UNIQUE INDEX `blog_categories_name_key`(`name`),
    UNIQUE INDEX `blog_categories_slug_key`(`slug`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `blog_posts` (
    `id` VARCHAR(191) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `excerpt` VARCHAR(500) NULL,
    `content` TEXT NOT NULL,
    `coverImageKey` VARCHAR(500) NULL,
    `categoryId` VARCHAR(191) NULL,
    `status` ENUM('DRAFT', 'PUBLISHED', 'ARCHIVED') NOT NULL DEFAULT 'DRAFT',
    `authorId` VARCHAR(50) NULL,
    `tags` TEXT NULL,
    `metaTitle` VARCHAR(255) NULL,
    `metaDesc` VARCHAR(500) NULL,
    `publishedAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `blog_posts_slug_key`(`slug`),
    INDEX `blog_posts_status_publishedAt_idx`(`status`, `publishedAt`),
    INDEX `blog_posts_slug_idx`(`slug`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `success_stories` (
    `id` VARCHAR(191) NOT NULL,
    `groomName` VARCHAR(100) NOT NULL,
    `brideName` VARCHAR(100) NOT NULL,
    `location` VARCHAR(150) NULL,
    `story` TEXT NOT NULL,
    `photoKey` VARCHAR(500) NULL,
    `marriageDate` DATE NULL,
    `isPublished` BOOLEAN NOT NULL DEFAULT false,
    `isFeatured` BOOLEAN NOT NULL DEFAULT false,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    INDEX `success_stories_isPublished_isFeatured_idx`(`isPublished`, `isFeatured`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `testimonials` (
    `id` VARCHAR(191) NOT NULL,
    `authorName` VARCHAR(100) NOT NULL,
    `authorRole` VARCHAR(100) NULL,
    `text` TEXT NOT NULL,
    `rating` INTEGER NOT NULL,
    `isPublished` BOOLEAN NOT NULL DEFAULT false,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `system_settings` (
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT NOT NULL,
    `updatedAt` DATETIME(3) NOT NULL,

    PRIMARY KEY (`key`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `saved_searches` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `filters` JSON NOT NULL,
    `alertEnabled` BOOLEAN NOT NULL DEFAULT false,
    `lastRanAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    INDEX `saved_searches_userId_idx`(`userId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `referral_codes` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `code` VARCHAR(20) NOT NULL,
    `usedCount` INTEGER NOT NULL DEFAULT 0,
    `creditsEarned` INTEGER NOT NULL DEFAULT 0,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    UNIQUE INDEX `referral_codes_userId_key`(`userId`),
    UNIQUE INDEX `referral_codes_code_key`(`code`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `divisions` ADD CONSTRAINT `divisions_countryId_fkey` FOREIGN KEY (`countryId`) REFERENCES `countries`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `districts` ADD CONSTRAINT `districts_divisionId_fkey` FOREIGN KEY (`divisionId`) REFERENCES `divisions`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `upazilas` ADD CONSTRAINT `upazilas_districtId_fkey` FOREIGN KEY (`districtId`) REFERENCES `districts`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `oauth_accounts` ADD CONSTRAINT `oauth_accounts_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `refresh_tokens` ADD CONSTRAINT `refresh_tokens_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profiles` ADD CONSTRAINT `profiles_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profiles` ADD CONSTRAINT `profiles_residingCountryId_fkey` FOREIGN KEY (`residingCountryId`) REFERENCES `countries`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profiles` ADD CONSTRAINT `profiles_homeDivisionId_fkey` FOREIGN KEY (`homeDivisionId`) REFERENCES `divisions`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profiles` ADD CONSTRAINT `profiles_homeDistrictId_fkey` FOREIGN KEY (`homeDistrictId`) REFERENCES `districts`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profiles` ADD CONSTRAINT `profiles_homeUpazilaId_fkey` FOREIGN KEY (`homeUpazilaId`) REFERENCES `upazilas`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `photos` ADD CONSTRAINT `photos_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `preferences` ADD CONSTRAINT `preferences_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `preferences` ADD CONSTRAINT `preferences_preferredDivisionId_fkey` FOREIGN KEY (`preferredDivisionId`) REFERENCES `divisions`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `preferences` ADD CONSTRAINT `preferences_preferredDistrictId_fkey` FOREIGN KEY (`preferredDistrictId`) REFERENCES `districts`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `verification_requests` ADD CONSTRAINT `verification_requests_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `verification_requests` ADD CONSTRAINT `verification_requests_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `profiles`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `interests` ADD CONSTRAINT `interests_senderId_fkey` FOREIGN KEY (`senderId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `interests` ADD CONSTRAINT `interests_receiverId_fkey` FOREIGN KEY (`receiverId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `conversations` ADD CONSTRAINT `conversations_interestId_fkey` FOREIGN KEY (`interestId`) REFERENCES `interests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `conversation_participants` ADD CONSTRAINT `conversation_participants_conversationId_fkey` FOREIGN KEY (`conversationId`) REFERENCES `conversations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `conversation_participants` ADD CONSTRAINT `conversation_participants_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `messages` ADD CONSTRAINT `messages_conversationId_fkey` FOREIGN KEY (`conversationId`) REFERENCES `conversations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `messages` ADD CONSTRAINT `messages_senderId_fkey` FOREIGN KEY (`senderId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `guardians` ADD CONSTRAINT `guardians_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `photo_access_requests` ADD CONSTRAINT `photo_access_requests_requesterId_fkey` FOREIGN KEY (`requesterId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `photo_access_requests` ADD CONSTRAINT `photo_access_requests_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `photo_access_requests` ADD CONSTRAINT `photo_access_requests_profileOwnerId_fkey` FOREIGN KEY (`profileOwnerId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `shortlists` ADD CONSTRAINT `shortlists_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `shortlists` ADD CONSTRAINT `shortlists_shortlistedId_fkey` FOREIGN KEY (`shortlistedId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profile_views` ADD CONSTRAINT `profile_views_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profile_views` ADD CONSTRAINT `profile_views_viewerId_fkey` FOREIGN KEY (`viewerId`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `match_scores` ADD CONSTRAINT `match_scores_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `match_scores` ADD CONSTRAINT `match_scores_candidateId_fkey` FOREIGN KEY (`candidateId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `subscriptions` ADD CONSTRAINT `subscriptions_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `subscriptions` ADD CONSTRAINT `subscriptions_planId_fkey` FOREIGN KEY (`planId`) REFERENCES `subscription_plans`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `payments` ADD CONSTRAINT `payments_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `payments` ADD CONSTRAINT `payments_planId_fkey` FOREIGN KEY (`planId`) REFERENCES `subscription_plans`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `contact_unlocks` ADD CONSTRAINT `contact_unlocks_unlockerId_fkey` FOREIGN KEY (`unlockerId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `contact_unlocks` ADD CONSTRAINT `contact_unlocks_unlockedId_fkey` FOREIGN KEY (`unlockedId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `contact_unlocks` ADD CONSTRAINT `contact_unlocks_paymentId_fkey` FOREIGN KEY (`paymentId`) REFERENCES `payments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profile_boosts` ADD CONSTRAINT `profile_boosts_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profile_boosts` ADD CONSTRAINT `profile_boosts_paymentId_fkey` FOREIGN KEY (`paymentId`) REFERENCES `payments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `notifications` ADD CONSTRAINT `notifications_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `reports` ADD CONSTRAINT `reports_reporterId_fkey` FOREIGN KEY (`reporterId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `reports` ADD CONSTRAINT `reports_reportedId_fkey` FOREIGN KEY (`reportedId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `blocks` ADD CONSTRAINT `blocks_blockerId_fkey` FOREIGN KEY (`blockerId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `blocks` ADD CONSTRAINT `blocks_blockedId_fkey` FOREIGN KEY (`blockedId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `admin_logs` ADD CONSTRAINT `admin_logs_adminId_fkey` FOREIGN KEY (`adminId`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `blog_posts` ADD CONSTRAINT `blog_posts_categoryId_fkey` FOREIGN KEY (`categoryId`) REFERENCES `blog_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
