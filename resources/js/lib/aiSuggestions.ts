/**
 * Writing-assistant suggestion engine for the biodata wizard.
 *
 * No external AI is configured, so this ships a SAFE, offline template bank that
 * adapts to: field type · platform mode (islamic / general) · language (en / bn).
 * It never throws and always returns at least one suggestion.
 *
 * FUTURE REAL-AI INTEGRATION: keep the React side calling `fetchSuggestions()`.
 * When an AI endpoint exists, replace that function's body with the API call and
 * fall back to `getSuggestions()` on any error/missing-config — the UI needs no
 * change.
 */

export interface SuggestionContext {
  /** Logical field key, e.g. 'hobbies', 'profession_details', 'partner_expectations'. */
  field: string
  /** Platform mode — 'islamic' gets deen-friendly wording, anything else is general. */
  mode?: string | null
  /** 'male' | 'female' — reserved for future tone tuning (unused by templates today). */
  gender?: string | null
  /** 'en' | 'bn' — defaults to English when unknown. */
  locale?: string | null
}

type Tone = 'islamic' | 'general'
type ToneBank = Record<Tone, string[]>
type LangBank = { en: ToneBank; bn: ToneBank }

/** Map a concrete field key onto the shared template bank that best fits it. */
function bankKeyFor(field: string): string {
  const f = field.toLowerCase()
  if (f.includes('hobb')) return 'hobbies'
  if (f.includes('future_career') || f.includes('career_plan')) return 'future_career_plan'
  if (f.includes('profession') || f.includes('occupation')) return 'profession_details'
  if (f.includes('family')) return 'family_details'
  if (f.includes('partner_deal') || f.includes('deal_breaker')) return 'partner_deal_breakers'
  if (f.includes('partner')) return 'partner_expectations'
  if (f.includes('why_getting_married') || f.includes('marriage_thought') || f.includes('marriage'))
    return 'marriage'
  if (f.includes('purdah') || f.includes('deen') || f.includes('religious') || f.includes('prayer'))
    return 'religious'
  if (f.includes('about')) return 'about'
  return '_default'
}

const TEMPLATES: Record<string, LangBank> = {
  hobbies: {
    en: {
      islamic: [
        'I enjoy reading Islamic books, learning new skills, spending time with family, and maintaining a balanced, halal lifestyle.',
        'I like productive activities — gaining beneficial knowledge, family time, and simple halal entertainment.',
        'I prefer meaningful hobbies such as reading, self-improvement, helping my family, and learning new things.',
      ],
      general: [
        'I enjoy learning new things, spending time with family, reading, and maintaining a simple and balanced lifestyle.',
        'I like reading, travelling, trying new skills, and spending quality time with people I care about.',
        'In my free time I enjoy productive hobbies such as reading, fitness, and learning something new.',
      ],
    },
    bn: {
      islamic: [
        'আমি ইসলামি বই পড়তে, উপকারী জ্ঞান অর্জন করতে, পরিবারকে সময় দিতে এবং হালাল ও পরিপাটি জীবনযাপন করতে পছন্দ করি।',
        'আমি অবসরে কুরআন তিলাওয়াত, বই পড়া, নতুন কিছু শেখা এবং পরিবারের সাথে সময় কাটাতে ভালোবাসি।',
        'আমি অর্থবহ কাজ যেমন পড়াশোনা, আত্ম-উন্নয়ন, পরিবারকে সাহায্য করা এবং নতুন কিছু শেখা পছন্দ করি।',
      ],
      general: [
        'আমি বই পড়তে, নতুন কিছু শিখতে, পরিবারকে সময় দিতে এবং সুন্দর ও পরিপাটি জীবনযাপন করতে পছন্দ করি।',
        'অবসরে আমি ভ্রমণ, বই পড়া এবং প্রিয় মানুষদের সাথে সময় কাটাতে ভালোবাসি।',
        'আমি উৎপাদনশীল শখ যেমন পড়াশোনা, শরীরচর্চা ও নতুন কিছু শেখা পছন্দ করি।',
      ],
    },
  },
  profession_details: {
    en: {
      islamic: [
        'I work in a stable, halal profession and try to be honest and sincere in my responsibilities.',
        'I am committed to my work while keeping a balance between my deen, family, and career.',
        'I take my profession seriously and aim to earn a halal income to support a family responsibly.',
      ],
      general: [
        'I work in a stable profession and take my responsibilities seriously, aiming to grow steadily in my career.',
        'I am dedicated and reliable in my work, and I value professionalism and continuous learning.',
        'I have a steady job that I enjoy, and I strive to balance my career with personal and family life.',
      ],
    },
    bn: {
      islamic: [
        'আমি একটি স্থিতিশীল ও হালাল পেশায় কাজ করি এবং দায়িত্ব পালনে সৎ ও আন্তরিক থাকার চেষ্টা করি।',
        'আমি দ্বীন, পরিবার ও পেশার মধ্যে ভারসাম্য রেখে আমার কাজে নিষ্ঠাবান।',
        'আমি হালাল উপার্জনের মাধ্যমে দায়িত্বশীলভাবে পরিবার চালানোর লক্ষ্যে কাজ করি।',
      ],
      general: [
        'আমি একটি স্থিতিশীল পেশায় কাজ করি এবং দায়িত্বকে গুরুত্ব দিয়ে ক্যারিয়ারে ধীরে ধীরে এগিয়ে যেতে চাই।',
        'আমি কাজে নিবেদিত ও নির্ভরযোগ্য এবং পেশাদারিত্ব ও ধারাবাহিক শেখাকে মূল্য দিই।',
        'আমার একটি স্থায়ী চাকরি আছে যা আমি উপভোগ করি এবং ক্যারিয়ার ও পরিবারের মধ্যে ভারসাম্য রাখতে চাই।',
      ],
    },
  },
  future_career_plan: {
    en: {
      islamic: [
        'I plan to grow steadily in my career while keeping my deen and family as my priority.',
        'I intend to build a stable, halal livelihood and support my family with honesty and balance.',
        'My goal is to develop my skills, increase a halal income, and contribute to my family and community.',
      ],
      general: [
        'I plan to keep growing in my career, develop new skills, and build a stable future for my family.',
        'I aim to progress steadily in my profession while maintaining a healthy work-life balance.',
        'My goal is to advance professionally and provide a secure, comfortable life for my family.',
      ],
    },
    bn: {
      islamic: [
        'আমি দ্বীন ও পরিবারকে অগ্রাধিকার দিয়ে ক্যারিয়ারে ধীরে ধীরে এগিয়ে যেতে চাই।',
        'আমি সততা ও ভারসাম্য রেখে একটি স্থিতিশীল হালাল উপার্জন গড়ে তুলতে চাই।',
        'আমার লক্ষ্য দক্ষতা বৃদ্ধি, হালাল আয় বৃদ্ধি এবং পরিবার ও সমাজে অবদান রাখা।',
      ],
      general: [
        'আমি ক্যারিয়ারে এগিয়ে যেতে, নতুন দক্ষতা অর্জন করতে এবং পরিবারের জন্য একটি স্থিতিশীল ভবিষ্যৎ গড়তে চাই।',
        'আমি কর্মজীবন ও ব্যক্তিগত জীবনের ভারসাম্য রেখে পেশায় ধারাবাহিকভাবে উন্নতি করতে চাই।',
        'আমার লক্ষ্য পেশাগতভাবে এগিয়ে যাওয়া এবং পরিবারকে একটি নিরাপদ ও স্বাচ্ছন্দ্যময় জীবন দেওয়া।',
      ],
    },
  },
  family_details: {
    en: {
      islamic: [
        'I come from a respectable, practising family that values deen, honesty, and strong relationships.',
        'My family is religious and supportive, and we value modesty, kindness, and mutual respect.',
        'We are a close-knit, God-conscious family that believes in simplicity and good character.',
      ],
      general: [
        'I come from a respectable, supportive family that values education, honesty, and strong relationships.',
        'My family is caring and down-to-earth, and we value mutual respect and good values.',
        'We are a close, friendly family that believes in honesty, simplicity, and helping one another.',
      ],
    },
    bn: {
      islamic: [
        'আমি একটি সম্মানিত ও দ্বীনদার পরিবার থেকে এসেছি, যেখানে দ্বীন, সততা ও সুসম্পর্ককে গুরুত্ব দেওয়া হয়।',
        'আমার পরিবার ধর্মপ্রাণ ও সহযোগিতাপূর্ণ এবং আমরা শালীনতা, দয়া ও পারস্পরিক সম্মানকে মূল্য দিই।',
        'আমরা একটি ঘনিষ্ঠ ও আল্লাহভীরু পরিবার যারা সরলতা ও উত্তম চরিত্রে বিশ্বাস করি।',
      ],
      general: [
        'আমি একটি সম্মানিত ও সহযোগিতাপূর্ণ পরিবার থেকে এসেছি, যেখানে শিক্ষা, সততা ও সুসম্পর্ককে গুরুত্ব দেওয়া হয়।',
        'আমার পরিবার যত্নশীল ও মাটির কাছাকাছি এবং আমরা পারস্পরিক সম্মান ও ভালো মূল্যবোধে বিশ্বাসী।',
        'আমরা একটি ঘনিষ্ঠ ও বন্ধুত্বপূর্ণ পরিবার যারা সততা, সরলতা ও একে অপরকে সাহায্য করায় বিশ্বাস করি।',
      ],
    },
  },
  marriage: {
    en: {
      islamic: [
        'I wish to marry to complete half of my deen and build a peaceful home based on mutual respect and taqwa.',
        'I am looking to start a family life founded on faith, trust, and shared Islamic values.',
        'I want a life partner to support each other in this world and the hereafter, with patience and love.',
      ],
      general: [
        'I am looking to settle down with a caring partner and build a peaceful, respectful home together.',
        'I wish to start a family life based on trust, understanding, and shared values.',
        'I want a supportive life partner to grow with, through both good times and challenges.',
      ],
    },
    bn: {
      islamic: [
        'আমি অর্ধেক দ্বীন পূর্ণ করতে এবং পারস্পরিক সম্মান ও তাকওয়ার ভিত্তিতে একটি শান্তিময় ঘর গড়তে বিবাহ করতে চাই।',
        'আমি ঈমান, বিশ্বাস ও ইসলামি মূল্যবোধের ভিত্তিতে একটি পারিবারিক জীবন শুরু করতে চাই।',
        'আমি এমন একজন জীবনসঙ্গী চাই যার সাথে ধৈর্য ও ভালোবাসা নিয়ে দুনিয়া ও আখিরাতে একে অপরকে সহায়তা করতে পারি।',
      ],
      general: [
        'আমি একজন যত্নশীল সঙ্গীর সাথে স্থিতু হয়ে একটি শান্তিময় ও সম্মানজনক ঘর গড়তে চাই।',
        'আমি বিশ্বাস, বোঝাপড়া ও অভিন্ন মূল্যবোধের ভিত্তিতে একটি পারিবারিক জীবন শুরু করতে চাই।',
        'আমি এমন একজন সহযোগী জীবনসঙ্গী চাই যার সাথে সুখ-দুঃখ ভাগ করে একসাথে এগিয়ে যেতে পারি।',
      ],
    },
  },
  partner_expectations: {
    en: {
      islamic: [
        'I am looking for a practising, kind-hearted partner with good character who values family and deen.',
        'I hope to find someone honest, respectful, and religiously committed, with whom I can grow closer to Allah.',
        'I value good manners, modesty, and sincerity more than worldly status in a life partner.',
      ],
      general: [
        'I am looking for a kind, honest, and respectful partner who values family and mutual understanding.',
        'I hope to find someone caring, supportive, and down-to-earth, with shared values and goals.',
        'I value good character, honesty, and a caring nature more than anything else in a partner.',
      ],
    },
    bn: {
      islamic: [
        'আমি একজন আমলদার, দয়ালু ও সচ্চরিত্রের সঙ্গী খুঁজছি, যিনি পরিবার ও দ্বীনকে মূল্য দেন।',
        'আমি এমন কাউকে চাই যিনি সৎ, শ্রদ্ধাশীল ও ধর্মপরায়ণ, যার সাথে আল্লাহর নিকটবর্তী হতে পারি।',
        'জীবনসঙ্গীর ক্ষেত্রে আমি দুনিয়াবি মর্যাদার চেয়ে উত্তম চরিত্র, শালীনতা ও আন্তরিকতাকে বেশি মূল্য দিই।',
      ],
      general: [
        'আমি একজন দয়ালু, সৎ ও শ্রদ্ধাশীল সঙ্গী খুঁজছি, যিনি পরিবার ও পারস্পরিক বোঝাপড়াকে মূল্য দেন।',
        'আমি এমন কাউকে চাই যিনি যত্নশীল, সহযোগী ও মাটির কাছাকাছি, অভিন্ন মূল্যবোধ ও লক্ষ্যসম্পন্ন।',
        'একজন সঙ্গীর মধ্যে আমি সর্বাগ্রে উত্তম চরিত্র, সততা ও যত্নশীল মনোভাবকে মূল্য দিই।',
      ],
    },
  },
  partner_deal_breakers: {
    en: {
      islamic: [
        'Dishonesty, lack of religious commitment, or disrespect towards family would not suit me.',
        'I would find it difficult to accept dishonesty, bad character, or neglect of deen.',
      ],
      general: [
        'Dishonesty, disrespect, or a lack of shared values would not work for me.',
        'I would find it difficult to accept dishonesty, bad temperament, or disrespect towards family.',
      ],
    },
    bn: {
      islamic: [
        'অসততা, দ্বীনের প্রতি উদাসীনতা বা পরিবারের প্রতি অসম্মান আমার জন্য মানানসই নয়।',
        'অসততা, মন্দ চরিত্র বা দ্বীন অবহেলা আমার পক্ষে মেনে নেওয়া কঠিন হবে।',
      ],
      general: [
        'অসততা, অসম্মান বা অভিন্ন মূল্যবোধের অভাব আমার জন্য মানানসই নয়।',
        'অসততা, বদমেজাজ বা পরিবারের প্রতি অসম্মান মেনে নেওয়া আমার পক্ষে কঠিন।',
      ],
    },
  },
  religious: {
    en: {
      islamic: [
        'I try to practise my deen sincerely — praying regularly, following the Sunnah, and improving my character.',
        'Alhamdulillah, I strive to maintain my five daily prayers and keep learning about my deen.',
        'I aim to live by Islamic values in daily life and continue growing in faith and good deeds.',
      ],
      general: [
        'I try to follow my faith sincerely and live by good values in everyday life.',
        'I respect religious practice and aim to keep improving myself as a person.',
        'Faith and good character are important to me, and I try to act on them daily.',
      ],
    },
    bn: {
      islamic: [
        'আমি আন্তরিকভাবে দ্বীন পালনের চেষ্টা করি — নিয়মিত সালাত আদায়, সুন্নাহ অনুসরণ ও চরিত্র সংশোধন।',
        'আলহামদুলিল্লাহ, আমি পাঁচ ওয়াক্ত সালাত আদায়ের চেষ্টা করি এবং দ্বীন শেখা অব্যাহত রাখি।',
        'আমি দৈনন্দিন জীবনে ইসলামি মূল্যবোধ অনুযায়ী চলতে এবং ঈমান ও নেক আমলে এগিয়ে যেতে চাই।',
      ],
      general: [
        'আমি আন্তরিকভাবে আমার ধর্ম পালনের চেষ্টা করি এবং দৈনন্দিন জীবনে ভালো মূল্যবোধ নিয়ে চলি।',
        'আমি ধর্মীয় চর্চাকে সম্মান করি এবং নিজেকে আরও ভালো মানুষ হিসেবে গড়তে চাই।',
        'ঈমান ও উত্তম চরিত্র আমার কাছে গুরুত্বপূর্ণ এবং আমি প্রতিদিন তা অনুসরণের চেষ্টা করি।',
      ],
    },
  },
  about: {
    en: {
      islamic: [
        'I am a practising, family-oriented person who values honesty, simplicity, and good character.',
        'I try to balance my deen, family, and work, and I believe in treating everyone with respect.',
        'I am sincere and easy-going, and I value a peaceful, halal lifestyle built on strong values.',
      ],
      general: [
        'I am a family-oriented, easy-going person who values honesty, simplicity, and good character.',
        'I try to keep a healthy balance between work and family, and I treat people with respect.',
        'I am sincere and down-to-earth, and I value a peaceful lifestyle built on strong values.',
      ],
    },
    bn: {
      islamic: [
        'আমি একজন আমলদার ও পরিবারমুখী মানুষ, যে সততা, সরলতা ও উত্তম চরিত্রকে মূল্য দেয়।',
        'আমি দ্বীন, পরিবার ও কাজের মধ্যে ভারসাম্য রাখার চেষ্টা করি এবং সবার সাথে সম্মানজনক আচরণে বিশ্বাসী।',
        'আমি আন্তরিক ও সহজ-সরল এবং দৃঢ় মূল্যবোধের ভিত্তিতে একটি শান্তিময় হালাল জীবন পছন্দ করি।',
      ],
      general: [
        'আমি একজন পরিবারমুখী ও সহজ-সরল মানুষ, যে সততা, সরলতা ও উত্তম চরিত্রকে মূল্য দেয়।',
        'আমি কাজ ও পরিবারের মধ্যে ভারসাম্য রাখার চেষ্টা করি এবং মানুষের সাথে সম্মানজনক আচরণ করি।',
        'আমি আন্তরিক ও মাটির কাছাকাছি এবং দৃঢ় মূল্যবোধের ভিত্তিতে শান্তিময় জীবন পছন্দ করি।',
      ],
    },
  },
  _default: {
    en: {
      islamic: [
        'I value honesty, family, and a balanced, halal lifestyle built on good character.',
        'I try to live by my deen sincerely while caring for my family and responsibilities.',
      ],
      general: [
        'I value honesty, family, and a simple, balanced lifestyle built on good character.',
        'I try to be sincere and responsible while caring for the people around me.',
      ],
    },
    bn: {
      islamic: [
        'আমি সততা, পরিবার এবং উত্তম চরিত্রের ভিত্তিতে একটি ভারসাম্যপূর্ণ হালাল জীবনকে মূল্য দিই।',
        'আমি আন্তরিকভাবে দ্বীন পালনের পাশাপাশি পরিবার ও দায়িত্বের যত্ন নেওয়ার চেষ্টা করি।',
      ],
      general: [
        'আমি সততা, পরিবার এবং উত্তম চরিত্রের ভিত্তিতে একটি সরল ও ভারসাম্যপূর্ণ জীবনকে মূল্য দিই।',
        'আমি আন্তরিক ও দায়িত্বশীল থাকার পাশাপাশি আশেপাশের মানুষের যত্ন নেওয়ার চেষ্টা করি।',
      ],
    },
  },
}

/**
 * Offline template suggestions for a field, adapted to mode + language.
 * Always returns at least one string; never throws.
 */
export function getSuggestions(ctx: SuggestionContext): string[] {
  const lang: 'en' | 'bn' = ctx.locale === 'bn' ? 'bn' : 'en'
  const tone: Tone = ctx.mode === 'islamic' ? 'islamic' : 'general'
  const fallback = TEMPLATES._default as LangBank
  const bank = (TEMPLATES[bankKeyFor(ctx.field)] ?? fallback) as LangBank
  const list = bank[lang][tone]
  return list.length > 0 ? list : fallback[lang][tone]
}

/**
 * Async entry point the UI calls. Today it just returns offline templates; when a
 * real AI endpoint is added, call it here and fall back to getSuggestions on error.
 */
export async function fetchSuggestions(ctx: SuggestionContext): Promise<string[]> {
  return getSuggestions(ctx)
}
