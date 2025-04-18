<!DOCTYPE html>
<html lang="{{ $autorisation_sortie_territoire ? 'fr' : 'ar' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decision congé {{ $conge->type === 'exceptionnel' ? 'exceptionnel' : 'annuel' }} {{ $fonctionnaire->nom }} {{ $fonctionnaire->prenom }}</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.print();
            var currentDate = new Date();

            var day = currentDate.getDate();
            var month = currentDate.getMonth() + 1;
            var year = currentDate.getFullYear();
            if (parseInt(month)>=1 && parseInt(month)<=9) {
                if (parseInt(day)>=1 && parseInt(day)<=9) {
                    var formattedDate = '0' + day + '-' + '0' + month + '-' + year;
                } else {
                    var formattedDate = day + '-' + '0' + month + '-' + year;
                }
            } else {
                if (parseInt(day)>=1 && parseInt(day)<=9) {
                    var formattedDate = '0' + day + '-' + month + '-' + year;
                } else {
                    var formattedDate = day + '-' + month + '-' + year;
                }
            }

            document.getElementById('currentdate').textContent = formattedDate.trim();
            document.getElementById('year').textContent = year;
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var number = {{ $conge->nombre_jours }};
            var arabicUnits = ["صفر", "واحد", "اثنان", "ثلاثة", "أربعة", "خمسة", "ستة", "سبعة", "ثمانية", "تسعة"];
            var arabicNumbers = [
                "أحد عشر",
                "اثنا عشر",
                "ثلاثة عشر",
                "أربعة عشر",
                "خمسة عشر",
                "ستة عشر",
                "سبعة عشر",
                "ثمانية عشر",
                "تسعة عشر"
            ];
            var arabicTens = ["", "عشرة", "عشرون", "ثلاثون", "أربعون", "خمسون", "ستون", "سبعون", "ثمانون", "تسعون"];
            var arabicHundreds = ["", "مئة", "مئتان", "ثلاثمئة", "أربعمئة", "خمسمئة", "ستمئة", "سبعمئة", "ثمانمئة", "تسعمئة"];

            var words = "";
            var hundred = "";
            var Ten = "";
            var Unit = "";
            var isNotUnits = false;
            var dayElement = document.getElementById("nombre_de_jours_ar");

            if (number >= 100) {
                var hundreds = Math.floor(number / 100);
                if (number % 100 === 0) {
                    hundred = arabicHundreds[hundreds];
                } else {
                    hundred = arabicHundreds[hundreds] + " و";
                }
                number %= 100;
            }

            if (number >= 20 || number === 10) {
                var tens = Math.floor(number / 10);
                Ten = arabicTens[tens] + " ";
                number %= 10;
                isNotUnits = true;
            }

            if (number < 20 && number > 10) {
                Ten = arabicNumbers[number % 10 -1] + " ";
            }

            if (number > 0 && number < 11 ) {
                if (isNotUnits) {
                    Unit = arabicUnits[number] + " و";
                } else {
                    if (number === 1 || number === 2) {
                        Unit = '';
                    } else {
                        Unit = arabicUnits[number];
                    }
                }
            }

            words = hundred + Unit + Ten;
            dayElement.textContent = words.trim();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var number = {{ $conge->nombre_jours }}; 

            var numberElement = document.getElementById("day_ar");
            if (number === 1) {
                numberElement.textContent = "يوم";
            } else if (number === 2) {
                numberElement.textContent = "يومين";
            } else if (number > 2 && number <= 10) {
                numberElement.textContent = " أيام";
            } else {
                numberElement.textContent = " يوما";
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var dateString = "{{ $conge->date_depart }}";
            var dateParts = dateString.split('-');
            var day = dateParts[0];
            var month = dateParts[1];
            var year = dateParts[2];
            var dateElement = document.getElementById("date_ar");

            var arabicMonths = [
                "",
                "يناير",
                "فبراير",
                "مارس",
                "أبريل",
                "ماي",
                "يونيو",
                "يوليوز",
                "غشت",
                "شتنبر",
                "أكتوبر",
                "نونبر",
                "دجنبر"
            ];

            function getArabicMonthName(index) {
                if (index >= 1 && index <= 12) {
                    return arabicMonths[index];
                }
                return "";
            }

            var arabicMonth = getArabicMonthName(parseInt(month));

            dateElement.textContent = year + ' ' + arabicMonth + ' ' + day;
            console.log(dateString);
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var number = {{ $conge->nombre_jours }};
            var frenchUnits = ["zéro", "un", "deux", "trois", "quatre", "cinq", "six", "sept", "huit", "neuf"];
            var frenchNumbers = [
                "onze",
                "douze",
                "treize",
                "quatorze",
                "quinze",
                "seize",
                "dix-sept",
                "dix-huit",
                "dix-neuf"
            ];
            var frenchTens = ["", "dix", "vingt", "trente", "quarante", "cinquante", "soixante", "soixante-dix", "quatre-vingt", "quatre-vingt-dix"];
            var frenchHundreds = ["", "cent", "deux-cents", "trois-cents", "quatre-cents", "cinq-cents", "six-cents", "sept-cents", "huit-cents", "neuf-cents"];
    
            if (number === 0) {
                return frenchUnits[0];
            }
    
            var words = "";
            var hundred = "";
            var Ten = "";
            var Unit = "";
            var temoin = false;
            var dayElement = document.getElementById("nombre_de_jours");
    
            if (number >= 100) {
                var hundreds = Math.floor(number / 100);
                if (number % 100 === 0) {
                    hundred = frenchHundreds[hundreds];
                } else {
                    hundred = frenchHundreds[hundreds] + " ";
                }
                number %= 100;
            }
    
            if (number >= 20 || number === 10) {
                var tens = Math.floor(number / 10);
                Ten = frenchTens[tens] + " ";
                number %= 10;
                if (number !== 0) {
                    temoin = true;
                } 
            }
    
            if (number < 20 && number > 10) {
                Ten = frenchNumbers[number % 10 - 1] + " ";
            }
    
            if (number > 0 && number <= 10) {
                if (number === 1 && temoin) {
                    Unit = "et " + frenchUnits[number];
                } else {
                    Unit = frenchUnits[number];
                }
            }
    
            words = hundred + Ten + Unit;
            dayElement.textContent = words.trim();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var number = {{ $conge->nombre_jours }}; 

            var numberElement = document.getElementById("day");
            if (number === 1) {
                numberElement.textContent = " jour ouvrable";
            } else {
                numberElement.textContent = "jours ouvrables";
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    function formatDate(dateString, elementId) {
        var dateParts = dateString.split('-');
        var day = dateParts[0];
        var month = dateParts[1];
        var year = dateParts[2];
        var dateElement = document.getElementById(elementId);

        var frenchMonths = [
            "", "janvier", "février", "mars", "avril", "mai", "juin",
            "juillet", "août", "septembre", "octobre", "novembre", "décembre"
        ];

        function getFrenchMonthName(index) {
            return (index >= 1 && index <= 12) ? frenchMonths[index] : "";
        }

        var frenchMonth = getFrenchMonthName(parseInt(month));
        if (dateElement) {
            dateElement.textContent = year + ' ' + frenchMonth + ' ' + day;
        }
    }

    formatDate("{{ $conge->date_depart }}", "date_depart");
    formatDate("{{ $conge->date_retour }}", "date_retour");
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

        .infos-right {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 15px;
            direction: {{ $autorisation_sortie_territoire ? 'ltr' : 'rtl' }}; 
            display: flex;
            align-items: center;
            justify-content: {{ $autorisation_sortie_territoire ? 'left' : 'right' }};
        }

        .infos-left {
            font-family: Arial, sans-serif;
            margin-{{ $autorisation_sortie_territoire ? 'left' : 'right' }}: 60px;
            margin-top: 5px;
            direction: {{ $autorisation_sortie_territoire ? 'ltr' : 'rtl' }};
            display: flex;
            flex-direction: column;
            align-items: flex-{{ $autorisation_sortie_territoire ? 'end' : 'start' }};
            font-size: 17px;
            margin-left: 40px;
            margin-top: 5px;
            flex-direction: column;
            align-items: flex-end;
        }

        .multiline-text {
            line-height: 1.5;
        }

        .header {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 28px;
            direction: {{ $autorisation_sortie_territoire ? 'ltr' : 'rtl' }};
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: underline;
        }

        .ligne1{
            font-family: Arial, sans-serif;
            line-height: 1;
            font-size: 20px;
            margin-{{ $autorisation_sortie_territoire ? 'left' : 'right' }}: 100px;
            direction: {{ $autorisation_sortie_territoire ? 'ltr' : 'rtl' }};
        }

        .custom-list {
            list-style: none;
            padding: 0;
            margin-{{ $autorisation_sortie_territoire ? 'left' : 'right' }}: 30px;
            direction: {{ $autorisation_sortie_territoire ? 'ltr' : 'rtl' }};
            display: flex;
            flex-direction: column;
            align-items: flex-{{ $autorisation_sortie_territoire ? 'end' : 'start' }};
        }

        .custom-list li {
            font-size: 20px;
            line-height: 1.5;
        }

        .custom-list li::before {
            content: "\25C6";
            font-size: 25px;
            margin-{{ $autorisation_sortie_territoire ? 'left' : 'right' }}: 5px;
            margin-{{ $autorisation_sortie_territoire ? 'right' : 'left' }}: 10px;
        }
        
        .chapter {
            font-family: Arial, sans-serif;
            font-size: 20px;
            direction: {{ $autorisation_sortie_territoire ? 'ltr' : 'rtl' }};
            margin-{{ $autorisation_sortie_territoire ? 'left' : 'right' }}: 30px;
            display: flex;
            align-items: flex-{{ $autorisation_sortie_territoire ? 'end' : 'start' }};
            text-decoration: underline
        }

        .decision {
            margin-{{ $autorisation_sortie_territoire ? 'left' : 'right' }}: 30px;
            direction: {{ $autorisation_sortie_territoire ? 'ltr' : 'rtl' }};
            font-size: 20px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="attestation">
        <p class="infos-right">
            @if($autorisation_sortie_territoire)
            <strong class="multiline-text">Royaume du Maroc<br>Ministère de l'Intérieur<br>Wilaya de la Région de l'Oriental<br>Région de l'Oriental<br>Direction Générale des Services<br>Direction des Affaires Administratives et Juridiques<br>Division des Ressources Humaines<br>et de l'Ingénierie de la Formation<br>Service de Gestion des Affaires du Personnel<br>N° : . . . .. . . . .<span id="year"></span></strong>
            @else
            <strong class="multiline-text">المملكة المغربية<br>وزارة الداخلية<br>ولاية جهة الشرق<br>جهة الشرق<br>المديرية العامة للمصالح<br>مديرية الشؤون الإدارية والقانونية<br>قسم الموارد البشرية<br>وهندسة التكوين<br>مصلحة تدبير شؤون الموظفين<br>عدد : . . . .. . . . .<span id="year"></span></strong>
            @endif
        </p>
        <div class="infos-left">
            @if($autorisation_sortie_territoire)
            <p><strong>Oujda le </strong><span></span></p>
            @else
            <p><strong>وجدة في </strong><span></span></p>
            @endif
        </div>
        <p class="header"></p>
            @if($autorisation_sortie_territoire)
            <strong>DÉCISION</strong>
            @else
            <strong>قرار</strong>
            @endif
        </p>
        <p class="ligne1">
            @if($autorisation_sortie_territoire)
            <strong>Le Président du Conseil Régional de l'Oriental</strong>
            @else
            <strong>ان رئيس مجلس جهة الشرق</strong>
            @endif
        </p>
        <ul class="custom-list">
            @if($autorisation_sortie_territoire)
            <li>Vu le Dahir <strong>n° 1.58.008</strong> du <strong>04 Chaaban 1377 (24 février 1958)</strong>, portant statut général de la fonction publique tel qu’il a été complété ou modifié par la loi n° 50-05, du 29 Rajab 1432 (18 février 2011) (en particulier les <strong>articles 40 et 41</strong>).</li>
            <li>Vu le Dahir <strong>n° 1.15.83</strong> du  <strong>07 Juillet 2015 </strong>portant promulgation de la loi n° 111/14 relative à l’organisation des régions.</li>
            <li>Vu la demande présentée par <strong>{{ $fonctionnaire->civilite }}. {{ $fonctionnaire->prenom }} {{ $fonctionnaire->nom }}, {{ $fonctionnaire->grade->libelle }}</strong> au sein de l’Administration de la Région de l’Oriental.</li>
            @else
            <li>بمقتضى الظهير الشريف <strong>1-رقم 008-58</strong> الصادر في  <strong>04 شعبان 1377 (24 فبراير 1958)</strong> بمثابة النظام الأساسي العام للوظيفة العمومية حسبما وقع تغييره وتتميمه بالقانون رقم 50-05 الصادر بتنفيذه الظهير الشريف رقم 1.11.87 الصادر في 29 رجب 1432 (18  فبراير 2011) (لاسيما <strong>الفصلين 40 و41</strong> منه)</li>
            <li>بمقتضى الظهير الشريف <strong>رقم 1.63.038 الصادر في 05 شوال 1382 (فاتح مارس 1963)</strong> بشأن النظام الأساسي الخصوصي للمتصرفين بوزارة الداخلية</li>
            <li>بناء على القانون التنظيمي <strong>رقم 14-111</strong> المتعلق بالجهات والصادر بتنفيذه الظهير الشريف <strong>رقم 83-15-1 بتاريخ 7 يوليوز 2015</strong> </li>
            <li>بناء على الأنظمة الأساسية الخاصة بالأطر المشتركة بين الوزارات</li>
            <li>بناء على المرسوم <strong>رقم 2.77.738 الصادر في 13 شوال 1397 (27 شتنبر 1977)</strong> بمثابة النظام الأساسي الخاص بموظفي الجماعات المحلية حسبما وقع تغييره تتميمه</li>
            <li>بناء على المنشور <strong>رقم 1.05 و ع بتاريخ 05 يونيو 2011</strong> المتعلق بالرخص السنوية</li>
            @endif
        </ul>
        <p class="header">
            @if($autorisation_sortie_territoire)
            <strong>Décide</strong>
            @else
            <strong>يقرر</strong>
            @endif
        </p>
        <p class="chapter">
            @if($autorisation_sortie_territoire)
            @else
            <strong>فصل فريد :</strong>
            @endif
        </p>
        <p class="decision">
            @if($autorisation_sortie_territoire)
            <strong>Article n°1</strong> : Un congé administratif d'une durée de <strong id="nombre_de_jours"></strong> <strong>( {{ $conge->nombre_jours }} )</strong> <strong id="day"></strong> est accordé à <strong> {{ $fonctionnaire->civilite }}. {{ $fonctionnaire->prenom }} {{ $fonctionnaire->nom }}</strong>, au titre de l'année <strong>{{ date('Y') }}</strong> à compter du <strong id="date_depart"></strong> au <strong id="date_retour"></strong><br><br>
            <strong>Article n°2</strong> : L’intéressé est autorisé de quitter le territoire national dans la limite de son congé administratif.
            @else
            تمنح رخصة ادارية سنوية مدتها  <strong id="nombre_de_jours_ar"></strong> <strong id="day_ar"></strong> <strong>( {{ $conge->nombre_jours }} )</strong> عمل عن سنة  <strong>{{ date('Y') }}</strong> ابتداء من <strong id="date_ar"></strong> للسيد(ة)  {{ $fonctionnaire->nom_ar }} {{ $fonctionnaire->prenom_ar }} موظف(ة) بادارة جهة الشرق
            @endif
        </p>
    </div>
</body>
</html>