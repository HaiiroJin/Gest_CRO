<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande congé {{ $fonctionnaire->nom }} {{ $fonctionnaire->prenom }}</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var number = {{ $conge->nombre_jours }}; // Mettez ici la valeur de $number

            var numberElement = document.getElementById("number");

            if (number >= 1 && number <= 10) {
                numberElement.textContent = number + " أيام";
            } else {
                numberElement.textContent = number + " يوما";
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var autorisation = {{ $conge->autorisation_sortie_territoire ? 'true' : 'false' }}; // Ensure boolean conversion

            var checkboxSans = document.getElementById("sans");
            var checkboxAvec = document.getElementById("avec");

            if (autorisation) {
                checkboxSans.removeAttribute('checked');
                checkboxAvec.setAttribute('checked', 'checked');
            } else {
                checkboxSans.setAttribute('checked', 'checked');
                checkboxAvec.removeAttribute('checked');
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var Type = "{{ $conge->type }}"; // Variable booléenne pour déterminer le Type
        
            var checkboxAnnuel = document.getElementById("annuel");
            var checkboxExcep = document.getElementById("excep");
        
            if (Type === "exceptionnel") {
                checkboxAnnuel.checked = false; // Cocher la case à cocher "مرفقة"
                checkboxExcep.checked = true; // Décocher la case à cocher "بدون"
            } else {
                checkboxAnnuel.checked = true; // Décocher la case à cocher "مرفقة"
                checkboxExcep.checked = false; // Cocher la case à cocher "بدون"
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.print();
        });
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #date {
            display: inline;
        }

        #number {
            display: inline;
            margin-right: 30px;
        }

        .attestation {
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            flex:1;
        }

        .infos-right {
            font-family: Arial, sans-serif;
            font-size: 17px;
            margin-right: 50px;
            margin-left: 10px;
            margin-bottom: 2px;
            direction: rtl;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 0.10;
        }


        .infos-left {
            font-family: Arial, sans-serif;
            font-size: 17px;
            margin-left: 40px;
            margin-top: 5px;
            direction: rtl;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .header {
            text-align: center;
            margin: 20px;
            margin-top: 30px;
            margin-bottom: 40px;
            font-family: Arial, sans-serif;
            font-size: 25px;
            direction: rtl; 
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox1 {
            font-family: Arial, sans-serif;
            font-size: 20px;
            margin-top: 10px;
            margin-right: 65px;
            direction: rtl;
            align-items: flex-start;
        }

        .checkbox2 {
            font-family: Arial, sans-serif;
            font-size: 20px;
            margin-right: 65px;
            direction: rtl;
            align-items: flex-start;
        }

        .checkbox1 label  {
            margin-left: 5px;
        }

        .checkbox2 label {
            margin-left: 5px;
        }

        .checkbox1 .input[type="CHECKBOX"] {
            width: 100px;
            height: 100px;

        }

        .checkbox2 .input[type="CHECKBOX"] {
            width: 100px;
            height: 100px;
            margin-right:1cm;
        }

        .text {
            line-height: 1;
            font-family: Arial, sans-serif;
            font-size: 20px;
            direction: rtl;
        }

        .ligne1 {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            
        }

        .ligne3 {
            margin-right: 65px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .container {
            font-family: Arial, sans-serif;
            font-size: 20px;
            display: flex;
            justify-content: space-between;
            margin-right: 90px;
        }

        .signature1 {
            text-align: left;
            margin-top: 30px;
            margin-left: 70px;
            text-decoration: underline;
        }

        .signature2 {
            text-align: right;
            margin-top: 30px;
            margin-left: 70px;
            text-decoration: underline;
        }

        table {
            border-collapse: collapse;
            margin-top: 100px;
            margin-left: auto; 
            margin-right: auto; 
        }
        th {
            line-height: 1;
            border: 1px solid black;
            padding: 8px;
        }

        .celui_signer {
            margin : 70px;
            text-decoration: underline;
        }

        .name_sign {
            margin-bottom: 130px;
        }

    </style>
</head>
<body>
    <div class="attestation">
        <div class="infos-right">
            <p><strong>الاسم الشخصي والعائلي : </strong><span> {{ $fonctionnaire->nom_ar }} {{ $fonctionnaire->prenom_ar }} </span></p>
            <p style="display: GStatus;"><strong>الدرجة : </strong><span> {{ $fonctionnaire->grade->libelle_ar ?? '' }} </span></p>
            <p style="display: CStatus;"><strong>القسم : </strong><span> {{ $fonctionnaire->division->libelle_ar ?? '' }} </span></p>
            <p style="display: SStatus;"><strong>المصلحة : </strong><span> {{ $fonctionnaire->service->libelle_ar ?? '' }} </span></p>
            <p style="display: DrStatus;"><strong>المديرية : </strong><span> {{ $fonctionnaire->direction->libelle_ar ?? '' }} </span></p>
        </div>
        <div class="infos-left">
            <p><strong> وجدة في : </strong><span> {{ $conge->date_demande }} </span></p>
        </div>
        <h1 class="header">الى<br>  <br> السيد رئيس مجلس جهة الشرق</h1>
        <div class="checkbox1">
            <p><strong> الموضوع </strong> : طلب الاستفادة من رخصة ادراية 
            <span> 
                <label for="annuel"> سنوية </label>
                <input TYPE=CHECKBOX UNCHECKED name="annuel" id="annuel">
            </span>
            <span>
                <label for="excep"> استثنائية </label>
                <input TYPE=CHECKBOX UNCHECKED name="excep" id="excep">
            </span>
            </p>
        </div>
        <div class="text">
            <p class="ligne1">سلام تام بوجود مولانا الامام</p>
            <p class="ligne3">و بعد يشرفني سيدي الرئيس أن ألتمس منكم الموافقة على طلبي المتعلق بالاستفادة من رخصة ادارية لمدة </p>
            <p><strong><span id="number"></span></strong> من أيام العمل ابتداء من <span id="date">{{ $conge->date_depart }}</span></p>
            <p class = "checkbox2">
                <span> 
                    <label for="avec"> مرفقة </label>
                    <input TYPE=CHECKBOX UNCHECKED name="avec" id="avec">
                </span>
                <span>
                    <label for="sans"> بدون </label>
                    <input TYPE=CHECKBOX UNCHECKED  name="sans" id="sans">
                </span>
                  برخصة مغادرة التراب الوطني عن نفس الفترة.
            </p>
            <p class="ligne3">وتقبلوا فائق عبارات التقدير والاحترام</p>
        </div>
        <div class="container">
                <p class="signature1"><strong>امضاء المعني بالأمر</strong></p>
                <p class="signature2"><strong>امضاء من ينوب عنه</strong></p>
        </div>
        <table>
            <tr>
                <th><strong class="celui_signer">المدير(ة)</strong><p class="name_sign">التوقيع والاسم</p></th>
                <th><strong class="celui_signer">رئيس(ة) القسم</strong><p class="name_sign">التوقيع والاسم</p></th>
                <th><strong class="celui_signer">رئيس(ة) المصلحة</strong><p class="name_sign">التوقيع والاسم</p></th>
            </tr>
        </table>
    </div>
</body>
</html>