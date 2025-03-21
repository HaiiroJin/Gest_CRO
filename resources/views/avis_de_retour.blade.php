<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avi de retour</title>
    <style>
        .fieldset {
            border: 3px solid #000;
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            font-size: 20px;
            text-align: right;
        }
        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }   
        .attestation {
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            direction: rtl;
        }
        .infos-right {
            font-family: Arial, sans-serif;
            font-size: 17px;
            margin-right: 60px;
            margin-top: 5px;
            direction: rtl;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: right;
            font-size: 17px;
            margin-right: 20px;
            flex: -1.4;
            padding: -20px;
            margin-left: 100px;
        }
        .multiline-text {
            line-height: 1.2;
            font-size: 20px;
            width: 100%;
            text-align: center;
            padding-left: 25px;
            padding-right: 10px;
        }
        .header {
            text-align: center;
            font-size: 20px;
            border: 5px solid #000;
            border-radius: 10px;
            box-shadow: -10px -10px 1px rgba(0, 0, 0, 0.2);
            padding: 10px 20px;
            background-color: #fff;
            flex: 1;
            max-width: 350px;
            margin-left: 40px;
            margin-right: -25px;
            padding-top: 10px;
            padding-bottom: 40px;
            margin-bottom: -40px;
        }
        .input {
            margin-top: 20px;
            min-height: 20px;
            padding: 5px;
            min-width: 200px;
            margin-left: 10px;
            height: 20px;
            font-size: large;
            font-weight: bold;
        }
        #president {
            font-size: 30px;
            font-weight: bold;
            padding-right: 40px;
            margin-top: 5px;
        }
        .signature {
            display: flex;
            justify-content: space-between;
            width: 100%;
            direction: rtl;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 40px;
        }
        #right {
            text-align: right;
            text-decoration: underline;
        }

        #left {
            text-align: left;
            text-decoration: underline;
            padding-left: 40px;
       }
    </style>
</head>
<body>
    <div class="attestation">
        <div class="container">
            <p class="infos-right">
                <strong class="multiline-text">المملكة المغربية<br>وزارة الداخلية<br>ولاية جهة الشرق<br>جهة الشرق<br>المديرية العامة للمصالح<br>مديرية الشؤون الإدارية والقانونية<br>قسم الموارد البشرية وهندسة التكوين<br>مصلحة تدبير شؤون الموظفين<br>عدد.........../<span id="year"></span></strong>
            </p>
            <p class="header">
                <strong>إشعار بالرجوع من رخصة</strong>
            </p>
        </div>
        <form>
            <fieldset class="fieldset">
                <p style="margin-left: 40px;">إن السيد(ة): <span class="input">{{ $fonctionnaire->nom_ar }} {{ $fonctionnaire->prenom_ar }}</span></p>
                <p style="margin-left: 40px;">الدرجة الإدارية: <span class="input">{{ $fonctionnaire->grade->libelle_ar }}</span></p>
                <p style="margin-left: 40px;">- قد رجع(ت) من رخصته(ها) يوم<span class="input">{{ $conge->date_retour }}</span></p>
                <p style="margin-left: 40px;">- بعد الاستفادة من : <strong>{{ $conge->nombre_jours }}</strong> أيام عمل من رخصة <strong>{{ $conge->type = 'annuelle' ? 'سنوية' : '' }}</strong>.</p>
                <p>بناء على: <span class="input">القرار......... بتاريخ ............</span></p>
                <div class="signature">
                    <p id="right">إمضاء الرئيس المباشر <span class="input"></span></p>
                    <p id="left">إمضاء المعني بالأمر <span class="input"></span></p>
                </div>
            </fieldset>
            <fieldset class="fieldset">
                <div class="signature">
                    <p id="right">رئيس القسم <span class="input"></span></p>
                    <p id="left">المدير المعني <span class="input"></span></p>
                </div>
            </fieldset>
            <fieldset class="fieldset">
                <p id="president">الرئيس <span class="input"></span></p>
                <p id="date" style="text-align: left; padding-top: 20px;">وجدة في {{ date('Y-m-d') }}<span class="input"></span></p>
            </fieldset>

        </form>
    </div>
</body>
</html>