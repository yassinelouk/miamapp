<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<title>{{$bs->website_title}}</title>
	<link rel="icon" href="{{asset('assets/front/img/'.$bs->favicon)}}">
    @yield('css')
	@includeif('admin.partials.styles')
    @php
        $selLang = App\Models\Language::where('code', request()->input('language'))->first();
    @endphp
    @if (!empty($selLang) && $selLang->rtl == 1)
    <style>
    #editModal form input,
    #editModal form textarea,
    #editModal form select {
        direction: rtl;
    }
    #editModal form .note-editor.note-frame .note-editing-area .note-editable {
        direction: rtl;
        text-align: right;
    }

    </style>
    @endif
<style>
    .alert:not(.normal){
        transform: scale(2.5) !important;
        top:10% !important;
        right:0 !important;
        left:0 !important;
        z-index: 1075 !important;
    }
    .card-round {
    border-radius: 30px;
    }
    a,button{
            border-radius: 15px !important;
            margin-left:5px !important;
    }
    .form-control:disabled, .form-control[readonly] {
    min-width: 70px;
}
    .logo-header .logo .navbar-brand {
    max-height: 60px;
}

</style>
</head>
<body data-background-color="dark">
	<div class="wrapper  sidebar_minimize @yield('sidebar')">

    {{-- top navbar area start --}}
    @includeif('admin.partials.top-navbar')
    {{-- top navbar area end --}}


    {{-- side navbar area start --}}
    @includeif('admin.partials.side-navbar')
    {{-- side navbar area end --}}


		<div class="main-panel">
        <div class="content">
            <div class="page-inner">
            @yield('content')
            </div>
        </div>
            @includeif('admin.partials.footer')
		</div>

	</div>

	@includeif('admin.partials.scripts')

    {{-- Loader --}}
    <div class="request-loader">
        <img src="{{asset('assets/admin/img/loader.gif')}}" alt="">
    </div>
    {{-- Loader --}}
    <script>
    var bgcolor = document.querySelectorAll('*[data-background-color= "dark"]');
    var bgcolor2 = document.querySelectorAll('*[data-background-color= "dark2"]');
    var textCol = document.querySelectorAll('.text-white');
    if (sessionStorage.getItem('theme') == null) {
        sessionStorage.setItem('theme', 'dark');
    }
    function switchTheme () {
        textCol.forEach(element => {
                if (sessionStorage.getItem('theme') === 'dark') {
                    element.classList.replace('text-white', 'text-black');
                }
                else {
                    element.classList.replace('text-black', 'text-white');
                }
            })
            bgcolor.forEach(element => {
                if(sessionStorage.getItem('theme') === 'dark') {
                    element.setAttribute('data-background-color', 'white');
                }
                else {
                    element.setAttribute('data-background-color', 'dark');
                }
            });
            bgcolor2.forEach(element => {
                if(sessionStorage.getItem('theme') === 'dark') {
                    element.setAttribute('data-background-color', 'white');
                }
                else {
                    element.setAttribute('data-background-color', 'dark2');
                }
            });
    }
    switchTheme();
        document.querySelector('.switch input[type=checkbox]').addEventListener('change', function () {
            if (sessionStorage.getItem('theme') === 'dark') {
                sessionStorage.setItem('theme', 'white');
            }
            else {
                sessionStorage.setItem('theme', 'dark');
            }
            switchTheme();
        })
    </script>
    <script>
    $('.timepicker').timepicker({
            timeFormat: 'HH:mm',
        });

          $(document).ready(function(){
            var dt_table = $('.table').DataTable({
            language:{
    "emptyTable": "Aucune donn??e disponible dans le tableau",
    "lengthMenu": "Afficher _MENU_ ??l??ments",
    "loadingRecords": "Chargement...",
    "processing": "Traitement...",
    "zeroRecords": "Aucun ??l??ment correspondant trouv??",
    "paginate": {
        "first": "Premier",
        "last": "Dernier",
        "previous": "Pr??c??dent",
        "next": "Suiv"
    },
    "aria": {
        "sortAscending": ": activer pour trier la colonne par ordre croissant",
        "sortDescending": ": activer pour trier la colonne par ordre d??croissant"
    },
    "select": {
        "rows": {
            "_": "%d lignes s??lectionn??es",
            "1": "1 ligne s??lectionn??e"
        },
        "cells": {
            "1": "1 cellule s??lectionn??e",
            "_": "%d cellules s??lectionn??es"
        },
        "columns": {
            "1": "1 colonne s??lectionn??e",
            "_": "%d colonnes s??lectionn??es"
        }
    },
    "autoFill": {
        "cancel": "Annuler",
        "fill": "Remplir toutes les cellules avec <i>%d<\/i>",
        "fillHorizontal": "Remplir les cellules horizontalement",
        "fillVertical": "Remplir les cellules verticalement"
    },
    "searchBuilder": {
        "conditions": {
            "date": {
                "after": "Apr??s le",
                "before": "Avant le",
                "between": "Entre",
                "empty": "Vide",
                "equals": "Egal ??",
                "not": "Diff??rent de",
                "notBetween": "Pas entre",
                "notEmpty": "Non vide"
            },
            "number": {
                "between": "Entre",
                "empty": "Vide",
                "equals": "Egal ??",
                "gt": "Sup??rieur ??",
                "gte": "Sup??rieur ou ??gal ??",
                "lt": "Inf??rieur ??",
                "lte": "Inf??rieur ou ??gal ??",
                "not": "Diff??rent de",
                "notBetween": "Pas entre",
                "notEmpty": "Non vide"
            },
            "string": {
                "contains": "Contient",
                "empty": "Vide",
                "endsWith": "Se termine par",
                "equals": "Egal ??",
                "not": "Diff??rent de",
                "notEmpty": "Non vide",
                "startsWith": "Commence par"
            },
            "array": {
                "equals": "Egal ??",
                "empty": "Vide",
                "contains": "Contient",
                "not": "Diff??rent de",
                "notEmpty": "Non vide",
                "without": "Sans"
            }
        },
        "add": "Ajouter une condition",
        "button": {
            "0": "Recherche avanc??e",
            "_": "Recherche avanc??e (%d)"
        },
        "clearAll": "Effacer tout",
        "condition": "Condition",
        "data": "Donn??e",
        "deleteTitle": "Supprimer la r??gle de filtrage",
        "logicAnd": "Et",
        "logicOr": "Ou",
        "title": {
            "0": "Recherche avanc??e",
            "_": "Recherche avanc??e (%d)"
        },
        "value": "Valeur"
    },
    "searchPanes": {
        "clearMessage": "Effacer tout",
        "count": "{total}",
        "title": "Filtres actifs - %d",
        "collapse": {
            "0": "Volet de recherche",
            "_": "Volet de recherche (%d)"
        },
        "countFiltered": "{shown} ({total})",
        "emptyPanes": "Pas de volet de recherche",
        "loadMessage": "Chargement du volet de recherche..."
    },
    "buttons": {
        "collection": "Collection",
        "colvis": "Visibilit?? colonnes",
        "colvisRestore": "R??tablir visibilit??",
        "copy": "Copier",
        "copySuccess": {
            "1": "1 ligne copi??e dans le presse-papier",
            "_": "%ds lignes copi??es dans le presse-papier"
        },
        "copyTitle": "Copier dans le presse-papier",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "-1": "Afficher toutes les lignes",
            "_": "Afficher %d lignes"
        },
        "pdf": "PDF",
        "print": "Imprimer",
        "copyKeys": "Appuyez sur ctrl ou u2318 + C pour copier les donn??es du tableau dans votre presse-papier."
    },
    "decimal": ",",
    "info": "Affichage de _START_ ?? _END_ sur _TOTAL_ ??l??ments",
    "infoEmpty": "Affichage de 0 ?? 0 sur 0 ??l??ments",
    "infoThousands": ".",
    "search": "Rechercher:",
    "thousands": ".",
    "infoFiltered": "(filtr??s depuis un total de _MAX_ ??l??ments)",
    "datetime": {
        "previous": "Pr??c??dent",
        "next": "Suivant",
        "hours": "Heures",
        "minutes": "Minutes",
        "seconds": "Secondes",
        "unknown": "-",
        "amPm": [
            "am",
            "pm"
        ],
        "months": {
            "0": "Janvier",
            "2": "Mars",
            "3": "Avril",
            "4": "Mai",
            "5": "Juin",
            "6": "Juillet",
            "7": "Aout",
            "8": "Septembre",
            "9": "Octobre",
            "10": "Novembre",
            "1": "F??vrier",
            "11": "D??cembre"
        },
        "weekdays": [
            "Dim",
            "Lun",
            "Mar",
            "Mer",
            "Jeu",
            "Ven",
            "Sam"
        ]
    },
    "editor": {
        "close": "Fermer",
        "create": {
            "title": "Cr??er une nouvelle entr??e",
            "submit": "Envoyer",
            "button": "Nouveau"
        },
        "edit": {
            "button": "Editer",
            "title": "Editer Entr??e",
            "submit": "Modifier"
        },
        "remove": {
            "button": "Supprimer",
            "title": "Supprimer",
            "submit": "Supprimer",
            "confirm": {
                "_": "??tes-vous s??r de vouloir supprimer %d lignes ?",
                "1": "??tes-vous s??r de vouloir supprimer 1 ligne ?"
            }
        },
        "error": {
            "system": "Une erreur syst??me s'est produite"
        },
        "multi": {
            "noMulti": "Ce champ peut ??tre ??dit?? individuellement, mais ne fait pas partie d'un groupe. ",
            "info": "Les ??l??ments s??lectionn??s contiennent diff??rentes valeurs pour ce champ. Pour  modifier et ",
            "title": "Valeurs multiples",
            "restore": "R??tablir modification"
        }
    }
}
            });
            let bulk_selector = $("input[data-val='all']");
            bulk_selector.change(function() {
                  var cells = dt_table.cells().nodes();
                  $(cells).find(':checkbox').prop('checked', $(this).is(':checked'));
              })
        });



    </script>

    <script>

    $(function ($) {
    "use strict";
        // Realtime Order Notification
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;

    var pusher = new Pusher(pusherAppKey, {
        cluster: pusherCluster
    });


    var channel = pusher.subscribe('order-placed-channel');
    channel.bind('order-placed-event', function (data) {
        if ($("#refreshOrder").length > 0) {
            $(".request-loader").addClass("show");
            $("#refreshOrder").load(location.href + " #refreshOrder", function () {
                $(".request-loader").removeClass("show");
            });
        }

        audio.play();

        // show notification
        var content = {};

        content.message = "{{__('New Order Received!')}}";
        content.title = "{{__('Success')}}";
        content.icon = 'fa fa-bell';


            $.notify(content, {
            type: 'Succ??s',
            placement: {
                from: 'top',
                align: 'center'
            },
            offset: {
                y: '100',
            },
            animate: {
            	enter: 'animated fadeInDown',
            	exit: 'animated fadeOutUp'
            },
            autoHide:false,
            delay: 0
        });
    });


    var waiterCallChannel = pusher.subscribe('waiter-called-channel');
    waiterCallChannel.bind('waiter-called-event', function (data) {
        waiterCallAudio.play();

        // show notification
        var content = {};

        content.message = '<strong class="text-danger">{{__("Table")}} - ' + data.table + '</strong> {{__("ask for waiter!")}}';
        content.title = "{{__('Need a waiter!')}}";
        content.icon = 'fa fa-bell';

        $.notify(content, {
            type: 'secondary',
            placement: {
                from: 'top',
                align: 'center'
            },
            delay: 0,
        });
    });

    });
    </script>
    @yield('js')
</body>
</html>
