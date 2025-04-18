<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'Attestation Travail {{ $fonctionnaire->nom }} {{ $fonctionnaire->prenom }}</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var Arabe = {{ $choix_arabe ? 'true' : 'false' }}; 
            var Francais = {{ $choix_francais ? 'true' : 'false' }};

            var checkboxArabe = document.getElementById("lang_arabic");
            var checkboxFrench = document.getElementById("lang_french");
        
            if (Arabe && !Francais) {
                checkboxArabe.checked = true;
                checkboxFrench.checked = false;
            } else if (!Arabe && Francais) {
                checkboxArabe.checked = false;
                checkboxFrench.checked = true;
            }
        });
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        .attestation {
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin: 20px;
            margin-top: 30px;
            margin-bottom: 40px;
            font-family: Arial, sans-serif;
            font-size: 28px;
            direction: rtl;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .infos-right {
            font-family: Arial, sans-serif;
            font-size: 18px;
            margin-right: 50px;
            margin-left: 10px;
            margin-bottom: 2px;
            direction: rtl;
            display: flex;
            flex-direction: column;
            align-items: flex-start; 
        }

        .infos-right p {
            margin-bottom: 0px;
        }

        .infos-left {
            font-family: Arial, sans-serif;
            font-size: 18px;
            margin-left: 40px;
            margin-top: 5px;
            direction: rtl;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .input1 {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 50px;
        }

        .checkbox {
            font-family: Arial, sans-serif;
            font-size: 23px;
            margin-top: 10px;
            margin-right: 60px;
            direction: rtl;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .checkbox label {
            margin-left: 10px;
        }

        .checkbox input[type="checkbox"] {
            width: 17px;
            height: 17px;
            margin-left: 20px;
        }

        .text {
            font-family: Arial, sans-serif;
            font-size: 23px;
            direction: rtl;
        }

        .ligne1 {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ligne2 {
            margin-right: 50px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .ligne3 {
            margin-right: 65px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .ligne4 {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .signature {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-top: 50px;
            margin-left: 70px;
            text-decoration: underline;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .attestation {
                margin: 0;
                padding: 20px;
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="attestation">
        <div class="infos-right">
            <p><strong>الاسم الشخصي : </strong><span>{{ $fonctionnaire->prenom_ar }}</span></p>
            <p><strong>الاسم العائلي : </strong><span>{{ $fonctionnaire->nom_ar }}</span></p>
            <p><strong>الدرجة : </strong><span>{{ $fonctionnaire->grade->libelle_ar }}</span></p>
            <p><strong>رقم البطاقة التعريف الوطنية  : </strong><span>{{ $fonctionnaire->cin }}</span></p>
        </div>
        <div class="infos-left">
            <p><strong> وجدة في : </strong></p>
        </div>
        <h1 class="header">الى<br><br>السيد رئيس مجلس جهة الشرق</h1>
        <div class="checkbox">
            <p><strong>الموضوع</strong> : طلب شهادة العمل 
            <span> 
                <label for="lang_arabic"> عربية </label>
                <input TYPE=CHECKBOX UNCHECKED name="lang_arabic" id="lang_arabic">
            </span>
            <span>
                <label for="lang_french"> فرنسية </label>
                <input TYPE=CHECKBOX UNCHECKED name="lang_french" id="lang_french">
            </span>
            </p>
        </div>
        <div class="text">
            <p class="ligne1">سلام تام بوجود مولانا الامام</p>
            <p class="ligne3">و بعد يشرفني سيدي الرئيس أن أطلب منكم منحي شهادة العمل, وذلك من أجل استعمالها لغرض اداري</p>
            <p class="ligne3">وتقبلوا فائق عبارات التقدير والاحترام,</p>
            <p class="ligne4">والسلام.</p>
            <p class="signature"><strong>إمضاء المعني بالأمر</strong></p>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>