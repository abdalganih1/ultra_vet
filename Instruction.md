9:1 Uncaught (in promise) Error: A listener indicated an asynchronous response by returning true, but the message channel closed before a response was received
9:1 Uncaught (in promise) Error: A listener indicated an asynchronous response by returning true, but the message channel closed before a response was received
9:1 Uncaught (in promise) Error: A listener indicated an asynchronous response by returning true, but the message channel closed before a response was received
9:1 Uncaught (in promise) Error: A listener indicated an asynchronous response by returning true, but the message channel closed before a response was received


لا تزال المشكلة
http://127.0.0.1:8000/stray-pets/data-entry/9

لقد اخبرتني بالقيام بما يلي
أعتذر مرة أخرى عن استمرار هذه المشكلة. لتشخيص الخطأ بشكل نهائي، قمت بإضافة تعليمات برمجية خاصة لتسجيل ما يحدث بالضبط عند تشغيل الصفحة.

**الرجاء اتباع الخطوات التالية بدقة:**

1.  قم بتشغيل الأمر `npm run dev` في الـ terminal.
2.  افتح الصفحة `http://127.0.0.1:8000/stray-pets/data-entry/9` في متصفح الويب.
3.  افتح "أدوات المطور" في المتصفح (عادةً عن طريق الضغط على `F12` أو `Ctrl+Shift+I`).
4.  انقر على تبويب "Console".
5.  **انسخ كل النص الذي يظهر في الـ Console وألصقه هنا في المحادثة.**

المعلومات التي ستظهر في الـ Console ستوضح لي بالضبط أين تكمن المشكلة، وبعدها سأتمكن من إصلاحها بشكل نهائي.

لكن حدث الخطأ المرفق
DOM fully loaded and parsed. Starting script...
app.js:8 axios is available on the window object.
app.js:63 Found animalDataForm. Initializing translations...
app.js:79 Setting up translation for #city_province <-> #city_province_en
app.js:57 Translation event listener attached to #city_province
app.js:57 Translation event listener attached to #city_province_en
app.js:79 Setting up translation for #relocation_place <-> #relocation_place_en
app.js:57 Translation event listener attached to #relocation_place
app.js:57 Translation event listener attached to #relocation_place_en
app.js:79 Setting up translation for #breed_name <-> #breed_name_en
app.js:57 Translation event listener attached to #breed_name
app.js:57 Translation event listener attached to #breed_name_en
app.js:79 Setting up translation for #color <-> #color_en
app.js:57 Translation event listener attached to #color
app.js:57 Translation event listener attached to #color_en
app.js:79 Setting up translation for #distinguishing_marks <-> #distinguishing_marks_en
app.js:57 Translation event listener attached to #distinguishing_marks
app.js:57 Translation event listener attached to #distinguishing_marks_en
app.js:31 Translating "حماه طريق حلب" from ar to en...
9:1 Access to XMLHttpRequest at 'https://libretranslate.de/translate' from origin 'http://127.0.0.1:8000' has been blocked by CORS policy: Response to preflight request doesn't pass access control check: Redirect is not allowed for a preflight request.
app.js:42 Translation API error: AxiosError {message: 'Network Error', name: 'AxiosError', code: 'ERR_NETWORK', config: {…}, request: XMLHttpRequest, …}
translateText @ app.js:42
await in translateText
(anonymous) @ app.js:50
(anonymous) @ app.js:22
setTimeout
(anonymous) @ app.js:22
app.js:33  POST https://libretranslate.de/translate net::ERR_FAILED
dispatchXhrRequest @ axios.js?v=db827006:1651
xhr @ axios.js?v=db827006:1531
dispatchRequest @ axios.js?v=db827006:2006
_request @ axios.js?v=db827006:2227
request @ axios.js?v=db827006:2118
httpMethod @ axios.js?v=db827006:2256
wrap @ axios.js?v=db827006:8
translateText @ app.js:33
(anonymous) @ app.js:50
(anonymous) @ app.js:22
setTimeout
(anonymous) @ app.js:22