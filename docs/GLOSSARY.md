# Glossary and language guide

This guide covers Qrx in English and Arabic (Modern Standard Arabic). Strings live in `lang/en.json` and `lang/ar.json`, with the English text as the key. Laravel's own messages live in `lang/{en,ar}/*.php`.

`TranslationParityTest` fails if a key exists in one language and not the other. `ArabicPagesTest` fails if English is left on any Arabic page.

## Terminology

| English | Arabic | Notes |
|---|---|---|
| QR code / QR codes | رمز QR / رموز QR | Never "كود" or "باركود". "QR" stays in Latin letters. |
| dynamic QR code | رمز QR ديناميكي | |
| scan (noun) / scans | عملية مسح / عمليات المسح | Counted with `trans_choice` (see Plurals). |
| scan (verb) | يمسح / مسح | "Scan analytics" → تحليلات عمليات المسح |
| unique visitors | الزوار الفريدون | |
| short link | الرابط المختصر | |
| plan / current plan | الخطة / الخطة الحالية | Not "باقة". |
| Free · Starter · Business · Agency (plan names) | المجانية · المبتدئة · الأعمال · الوكالات | Definite feminine forms agree with an implied "الخطة". Never compose them into a sentence such as "خطة :plan"; show the name under a "Current plan" label. |
| subscription | الاشتراك | |
| upgrade (button) / upgrade (verb) | ترقية / رقِّ | |
| billing | الفوترة | "Plan & billing" → الخطة والفوترة |
| dashboard / overview | لوحة التحكم / نظرة عامة | |
| admin panel | لوحة الإدارة | |
| profile | الملف الشخصي | |
| type (of QR code) | النوع | Website = موقع ويب, Text = نص, Phone call = مكالمة هاتفية, SMS = رسالة نصية, Email = بريد إلكتروني, Contact card = بطاقة اتصال, Wi-Fi = واي فاي, Location = موقع جغرافي |
| Wi-Fi | واي فاي (الواي فاي in running text) | Arabic letters everywhere in Arabic copy; never mix in the Latin form. |
| contact card (vCard) | بطاقة اتصال | "vCard" only where the file format matters. |
| design / style / preset | التصميم / النمط / نمط جاهز | |
| module style / corner style | شكل النقاط / شكل الزوايا | |
| code colour / background / gradient | لون الرمز / الخلفية / تدرج لوني | |
| quiet zone | الهامش | |
| contrast | التباين | |
| preview | معاينة | |
| download | تنزيل | Not "تحميل" (which also means upload). |
| active / paused / limit reached | نشط / متوقف مؤقتًا / بلغ الحد | |
| pause / resume | إيقاف مؤقت / استئناف | |
| analytics / devices / top countries | التحليلات / الأجهزة / أكثر الدول | |
| example data | بيانات توضيحية | Used on the landing page's analytics preview. |
| log in / log out | تسجيل الدخول / تسجيل الخروج | English always says "log in", never "sign in". |
| create an account | أنشئ حسابًا | |
| email address / password | عنوان البريد الإلكتروني / كلمة المرور | English writes "email", never "e-mail". |

## Arabic style rules

- **Register:** Modern Standard Arabic, direct and friendly. Address the user as "أنت" (masculine as the neutral default) and avoid dialect.
- **Buttons and menu items use the verbal noun (مصدر):** حفظ، حذف، تعديل، تنزيل، إلغاء، إنشاء رمز QR، ترقية خطتك.
- **Marketing calls to action use the imperative:** أنشئ، جرّب، ابدأ مجانًا، تواصل معنا.
- **No "قم بـ…" calques.** Write "رقِّ خطتك" or "ترقية خطتك", not "قم بترقية خطتك".
- **Passive "تم + مصدر" only for short success confirmations** (تم الحفظ، تم النسخ، تم حذف رمز QR). In sentences, use the verb: "أُلغي اشتراكك", "نُقلت الصفحة".
- **Punctuation:** Arabic comma «،», semicolon «؛», question mark «؟», and quotation marks «».
- **Tanween on alif is written "ًا"** (مجانًا، حسابًا), not "اً".
- **Digits:** Western digits (0–9) in both languages. Numbers are formatted with `Number::format`, prices with `App\Support\Money` ("$29", the same in both languages). Phone numbers, codes, emails and links are wrapped in `dir="ltr"`.
- **Dates** use `translatedFormat`: Arabic month names with Western digits, for example "9 أكتوبر 2026".
- **Plurals:** Arabic has six forms. Use `trans_choice` with explicit ranges, for example `{0} لا توجد عمليات مسح|{1} عملية مسح واحدة|{2} عمليتا مسح|[3,10] :count عمليات مسح|[11,*] :count عملية مسح`.
- **Latin words kept as-is:**
  - brands: Qrx, Google, Apple, Facebook, Stripe;
  - technical tokens: QR, PNG, SVG, EPS, IP, WPA/WEP, vCard.

  Anything else must be translated; the Arabic page test enforces this. Data identifiers shown in the admin (slugs, Stripe IDs) carry `translate="no"`.

## English style rules

- **British spelling,** matching the brand voice: colour, organisation, cancelled.
- **Sentence case** for headings, buttons and labels ("Create QR code", not "Create QR Code").
- **Short, plain sentences** that address the user as "you". Say what happens: "Printed copies will stop working", not "This action is irreversible".
- **Copy edits go in `lang/en.json` values;** keys stay stable so the Arabic file and code lookups don't change. Example: the key "Organization" displays as "Organisation".

## RTL conventions (for translators and developers)

- Layout uses logical properties only (`ms-`/`me-`/`ps-`/`pe-`/`start-`/`end-`); `LogicalPropertiesTest` enforces it.
- **Directional icons are mirrored** in Arabic: arrows, chevrons, "open in new tab", "send". `<x-ui.button icon=…>` does this automatically, and other icons take `rtl:-scale-x-100`. Check marks, media and brand icons are not mirrored.
- **Time-series charts run in the reading direction:** in Arabic the newest day is on the left.
- **Illustrations contain no text,** so they can be mirrored whole.
- **Emails render with `dir="rtl"`** for Arabic recipients and are sent in each user's saved language.
- Email, link, phone and coordinate fields stay `dir="ltr"` and line up with the other fields at the end side.
