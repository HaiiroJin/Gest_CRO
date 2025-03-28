<!DOCTYPE html>
<html lang="{{ $langue }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attestation de travail {{ $fonctionnaire->nom }} {{ $fonctionnaire->prenom }}</title>
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

        .attestation {
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-sizing: border-box;
        }

        .infos-left {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 15px;
            direction: {{ $langue === 'ar' ? 'rtl' : 'ltr' }}; 
            display: flex;
            align-items: center;
            justify-content: {{ $langue === 'ar' ? 'right' : 'left' }};
        }

        .infos-right {
            font-family: Arial, sans-serif;
            font-size: 15px;
            margin-{{ $langue === 'ar' ? 'right' : 'left' }}: 60px;
            margin-top: 5px;
            direction: {{ $langue === 'ar' ? 'rtl' : 'ltr' }};
            display: flex;
            flex-direction: column;
            align-items: flex-{{ $langue === 'ar' ? 'start' : 'end' }};
        }

        .multiline-text {
            line-height: 1.5;
        }

        .header {
            text-align: center;
            font-family: Arial, sans-serif;
            margin-top: 40px;
            margin-bottom: 80px;
            font-size: 30px;
            direction: {{ $langue === 'ar' ? 'rtl' : 'ltr' }};
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .title {
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            width: 400px;
        }

        .body {
            font-family: Arial, sans-serif;
            line-height: 1;
            font-size: 20px;
            margin-{{ $langue === 'ar' ? 'left' : 'right' }}: 40px;
            direction: {{ $langue === 'ar' ? 'rtl' : 'ltr' }};
        }
    </style>
</head>
<body>
    <div class="attestation">
        <p class="infos-left">
            @if($langue === 'fr')
            <strong class="multiline-text">ROYAUME DU MAROC<br>MINISTERE DE L'INTERIEUR<br>WILAYA DE LA REGION DE L’ORIENTAL<br>REGION DE L’ORIENTAL<br>Direction Générale des Services<br>Direction des Affaires Administratives et Juridiques<br>Division des Ressources Humaines<br>et de l’Ingénierie de la Formation<br>Service de Gestion des Affaires du Personnel<br>N° : . . . .. . . . .<span id="year"></span></strong>
            @else
            <strong class="multiline-text">المملكة المغربية<br>وزارة الداخلية<br>ولاية جهة الشرق<br>جهة الشرق<br>المديرية العامة للمصالح<br>مديرية الشؤون الإدارية والقانونية<br>قسم الموارد البشرية<br>وهندسة التكوين<br>مصلحة تدبير شؤون الموظفين<br>عدد : . . . .. . . . .<span id="year"></span></strong>
            @endif
        </p>
        <div class="infos-right">
            @if($langue === 'fr')
            <p><strong>Oujda le </strong>{{ $attestation->date_demande->format('d-m-Y') }}</p>
            @else
            <p><strong>وجدة في </strong>{{ $attestation->date_demande->format('d-m-Y') }}</p>
            @endif
        </div>
        <div class="header">
            @if($langue === 'fr')
            <strong class="title">attestation de travail</strong>
            @else
            <strong class="title">شهادة العمل</strong>
            @endif
        </div>
        <div class="body">
            @if($langue === 'fr')
            <p>Le Président du Conseil de la Région de l'Oriental atteste que :</p>
            <p style="margin-left: 40px;">Prénom et Nom : <span style="margin-left: 30px;">{{ $fonctionnaire->nom }} {{ $fonctionnaire->prenom }}</span></p>
            <p style="margin-left: 40px;">N° C.N.I : <span style="margin-left: 30px;">{{ $fonctionnaire->cin }}</span></p>
            <p style="margin-left: 40px;">Corps : <span style="margin-left: 30px;">{{ $fonctionnaire->corps->libelle }}</span></p>
            <p style="margin-left: 40px;">Grade : <span style="margin-left: 30px;">{{ $fonctionnaire->grade->libelle }}</span></p>
            <p style="margin-left: 40px;">N° Matricule dans le système «Aujour» : <span style="margin-left: 30px;">{{ $fonctionnaire->matricule_aujour }}</span></p>
            <p>est en fonction à l'administration du Conseil de la Région de l'Oriental.</p>
            <p>La présente attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.</p>
            @else
            <p>يشهد رئيس مجلس جهة الشرق بأن السيد(ة) :</p>
            <p style="margin-right: 40px;">الاسم الكامل : <span style="margin-right: 30px;">{{ $fonctionnaire->prenom_ar }} {{ $fonctionnaire->nom_ar }}</span></p>
            <p style="margin-right: 40px;">رقم البطاقة الوطنية : <span style="margin-right: 30px;">{{ $fonctionnaire->cin }}</span></p>
            <p style="margin-right: 40px;">الإطار : <span style="margin-right: 30px;">{{ $fonctionnaire->corps->libelle_ar }}</span></p>
            <p style="margin-right: 40px;">الدرجة : <span style="margin-right: 30px;">{{ $fonctionnaire->grade->libelle_ar }}</span></p>
            <p style="margin-right: 40px;">رقـم التسجيل في منظومة أجور : <span style="margin-right: 30px;">{{ $fonctionnaire->matricule_aujour }}</span></p>
            <p>سلمت هذه الشهادة للمعني(ة) بالأمر للإدلاء بها عند الحاجة.</p>
            @endif
        </div>
    </div>
</body>
</html>