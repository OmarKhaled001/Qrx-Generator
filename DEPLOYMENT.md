# النشر على Hostinger

الدومين: **https://qrx-generator.omarkhaled.info**، والكود على السيرفر في `/home/u952344267/qrx-generator`.

## المسارات

| الحاجة | المسار |
|---|---|
| الكود (نسخة من الريبو) | `/home/u952344267/qrx-generator` |
| ملف الإعدادات (فيه الأسرار، مش في git) | `/home/u952344267/qrx-generator/.env` |
| فولدر الويب بتاع السب دومين | `/home/u952344267/domains/omarkhaled.info/public_html/qrx-generator` |
| | ده **symlink** لـ `/home/u952344267/qrx-generator/public` |
| الصفحة الافتراضية القديمة بتاعة Hostinger | `/home/u952344267/qrx-generator.default-page.bak` |
| اللوجز | `/home/u952344267/qrx-generator/storage/logs/laravel-YYYY-MM-DD.log` |
| مفتاح الـ deploy (قراءة بس من GitHub) | `~/.ssh/qrx-generator_deploy`، والـ alias بتاعه `github-qrx-generator` في `~/.ssh/config` |

الريبو: `OmarKhaled001/Qrx-Generator`، والبرانش `master`.

## الدخول على السيرفر

```sh
ssh -p 65002 u952344267@31.170.164.169
```

## تحديث الموقع (كل مرة)

السيرفر مفيهوش Node، فالـ CSS والـ JS بيتبنوا على جهازك ويترفعوا مع الكود في `public/build`.

```sh
# 1. على جهازك
npm run build
git add -A && git commit -m "..."
git push origin master

# 2. النشر
ssh -p 65002 u952344267@31.170.164.169 'cd ~/qrx-generator && ./deploy.sh'
```

`deploy.sh` بيعمل الخطوات دي بالترتيب:

1. بيحط الموقع في وضع الصيانة.
2. بيسحب آخر كود.
3. بيسطّب الباكدجات من غير dev.
4. بيشغّل الـ migrations.
5. بيعمل cache للإعدادات والراوتس.
6. بيرجّع الموقع تاني.

لو حاجة وقعت في النص، الموقع بيرجع لوحده (`trap`).

## الكرون (Cron)

التطبيق دلوقتي **مفيهوش أي scheduled tasks**، فالكرون مش ضروري. لو اتضافت مهام بعدين في `routes/console.php`، ضيف السطر ده من hPanel → Advanced → Cron Jobs، كل دقيقة:

```
cd /home/u952344267/qrx-generator && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

## حاجات مهمة

- **الطوابير (Queue):** `QUEUE_CONNECTION=sync`. مفيش حاجة في التطبيق بتتحط في queue، والاستضافة المشتركة مش بتسيب worker شغال.
- **Composer:** السيرفر قافل `proc_open`، فلازم يشتغل بالشكل ده (وده اللي `deploy.sh` بيعمله):
  ```sh
  php -d disable_functions= -d memory_limit=-1 "$(command -v composer)" install --no-dev --prefer-dist --optimize-autoloader --no-interaction
  ```
- **رابط الـ storage:** `php artisan storage:link` مش هيشتغل (الـ `symlink` مقفولة في PHP)، فاعمله بإيدك من فولدر المشروع:
  ```sh
  ln -s ../storage/app/public public/storage
  ```
- **`APP_KEY`:** متغيرهوش أبدًا بعد ما يتعمل. البيانات المشفرة هتبوظ.
- **الإيميل:** بيبعت من `smtp.hostinger.com` على بورت 465 (`MAIL_SCHEME=smtps`). الباسورد بتاع صندوق البريد بيتكتب في `.env` على السيرفر بس.

## أول أدمن

مفيش أدمن افتراضي في production. سجّل حساب عادي من الموقع، أو أنشئ واحد مباشرة:

```sh
ssh -t -p 65002 u952344267@31.170.164.169 'cd ~/qrx-generator && php artisan app:make-admin you@example.com'
```

الأمر هيسألك على الاسم والباسورد لو الحساب مش موجود. بعدها لوحة الأدمن على https://qrx-generator.omarkhaled.info/admin

## Stripe والدخول بجوجل وفيسبوك

- **مفاتيح Stripe:** حطّ `STRIPE_KEY` و`STRIPE_SECRET` و`STRIPE_WEBHOOK_SECRET` في `.env`.
- **أسعار الخطط:** حطّ IDs الأسعار في `STRIPE_PRICE_*`، وبعدها شغّل:
  ```sh
  php artisan db:seed --class=PlanSeeder --force && php artisan optimize
  ```
- **Webhook في Stripe:** `https://qrx-generator.omarkhaled.info/stripe/webhook`
- **روابط الرجوع (callback):**
  - جوجل: `https://qrx-generator.omarkhaled.info/auth/google/callback`
  - فيسبوك: `https://qrx-generator.omarkhaled.info/auth/facebook/callback`

## لو حاجة باظت

- **اللوجز:**
  ```sh
  tail -n 50 ~/qrx-generator/storage/logs/laravel-$(date +%F).log
  ```
- **مسح الكاش بعد تعديل `.env` يدويًا:**
  ```sh
  cd ~/qrx-generator && php artisan optimize:clear && php artisan optimize
  ```
- **لو الموقع فضل في وضع الصيانة:**
  ```sh
  cd ~/qrx-generator && php artisan up
  ```
