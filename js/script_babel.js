'use strict';

$(document).ready(function () {

    // Initialize popups
    $('#delete_popup').popup();

    $('#editUmfrage_popup').popup();

    $('#chart_popup').popup({
        onclose: function onclose() {
            $.ajax({
                url:'./php/ajax.php?action=umfragenDeaktivieren',
                complete: function (response) {
                    Server.Transfer(Request.RawUrl);
                },
                error: function () {
                }
            });

        }
    });

    //Popups öffnen
    if (typeof $_POST !== 'undefined' && $_POST !== null) {
        //Umfrage hinzufügen Popup bei Fehler öffnen
        if (typeof $_POST['no_frage'] !== 'undefined' || typeof $_POST['less_2_aw'] !== 'undefined' || typeof $_POST['no_timer'] !== 'undefined' || typeof $_POST['short_timer'] !== 'undefined') {
            $('#addUmfrage_popup').popup('show');
        } else {
            $('#addUmfrage_popup').popup();
        }
        //Dozent bearbeiten Popup bei Fehler öffnen
        if (typeof $_POST['no_name_edit'] !== 'undefined' || typeof $_POST['no_link_edit'] !== 'undefined' || typeof $_POST['name_exists_edit'] !== 'undefined' || typeof $_POST['link_exists_edit'] !== 'undefined' || typeof $_POST['no_pass_old'] !== 'undefined' || typeof $_POST['no_pass_new_1'] !== 'undefined' || typeof $_POST['no_pass_new_2'] !== 'undefined' || typeof $_POST['wrong_pass_old'] !== 'undefined' || typeof $_POST['pass_new_no_match'] !== 'undefined' || typeof $_POST['pass_new_short'] !== 'undefined') {
            $('#editDozent_popup').popup('show');
        } else {
            $('#editDozent_popup').popup();
        }

        //Umfrage bearbeiten anzeigen
        if (typeof $_POST['showEditUmfrage'] !== 'undefined') {
            $('#editUmfrage_popup').popup('show');
        }
        if (typeof $_POST['umfrage_edit_id'] !== 'undefined') {
            var form = '#showEditUmfrage_' + $_POST['umfrage_edit_id'];
            $(form).trigger('submit');
        }

        //Ergebnisse anzeigen
        if (typeof $_POST['showResultUmfrage'] !== 'undefined') {
            $.ajax({
                type: "GET",
                url: './php/ajax.php?umfrage_id_ergebnis=' + $_POST['showResultUmfrage'],
                success: function success(result) {
                    console.log(result);
                    var ergebnis = jQuery.parseJSON(result);

                    var title = ergebnis[0].frage;
                    var type = void 0;
                    var datapoints = [];

                    if (ergebnis[0].visualisierung == 0) {
                        type = "column";
                        $.each(ergebnis, function (index, value) {
                            datapoints.push({ label: value.antwort, y: parseInt(value.anzahl) });
                        });
                    } else if (ergebnis[0].visualisierung == 1) {
                        type = "pie";
                        $.each(ergebnis, function (index, value) {
                            datapoints.push({ indexLabel: value.antwort, y: parseInt(value.anzahl) });
                        });
                    }
                    var chart = new CanvasJS.Chart("chartContainer", {
                        title: {
                            text: title
                        },
                        legend: {
                            maxWidth: 350,
                            itemWidth: 120
                        },
                        data: [{
                            type: type,
                            dataPoints: datapoints
                        }]
                    });
                    if (ergebnis[0].visualisierung == 1) {

                        chart.options.data[0].showInLegend = true;
                        chart.options.data[0].legendText = "{indexLabel}";
                    }

                    chart.render();
                }
            });

            $('#chart_popup').popup('show');
        }
    } else {
        $('#addUmfrage_popup').popup();
        $('#editDozent_popup').popup();
    }

    //Bei Verbindung eines Users
    if ($_GET !== null && typeof $_GET['link'] !== 'undefined') {
        var counter;

        (function () {
            //setTimeout("location.reload(true);",1000);

            var state = 0;
            //AJAX Aufruf

            $(function ajaxUser() {
                $.ajax({
                    type: "GET",
                    url: './php/ajax.php?link=' + $_GET['link'],
                    success: function success(result) {
                        //Überprüfen ob Aufruf ein Ergebnis hat
                        if (result !== null && result !== 'undefined' && result !== "") {
                            var umfrage = jQuery.parseJSON(result);

                            //Überprüfen ob User die Frage beantwortet hat
                            var answered = 0;
                            console.log(umfrage.umfrage_id);
                            console.log(getCookie(umfrage.umfrage_id));
                            if (getCookie(umfrage.umfrage_id) == "") {
                                setCookie(umfrage.umfrage_id, 0, 1);
                            } else if (getCookie(umfrage.umfrage_id) == 1) {
                                answered = 1;
                            }

                            if (answered == 0 && state != umfrage.aktiv) {
                                (function () {
                                    //Aktuelle Frage abrufen
                                    //Wird nur abgerufen, wenn die Frage noch nicht beantwortt wurde oder der Status sich ändert
                                    counter = umfrage.timer;

                                    $("#frage").html(umfrage.frage);

                                    $("#umfrage_id").attr("value", umfrage.umfrage_id);

                                    var $insert = $("<div>");

                                    //Antworten laden
                                    $.each(umfrage.antworten, function ($index, $value) {
                                        var $indexNumber = parseInt($index) + 1;
                                        var $div = $("<div>", {class: "umfrage_element"});

                                        var $answer = $("<input>", { type: "radio", value: $value.antwort, name: "aw", id: 'aw' + $indexNumber });
                                        var $label = $("<label>", { for: 'aw' + $indexNumber });
                                        $label.text($value.antwort);
                                        $div.append($answer);
                                        $div.append($label);
                                        $insert.append($div);
                                    });
                                    $("#antworten").append($insert);
                                    state = 1;

                                    $("#timer_user").html(counter + " Sekunden übrig");
                                })();
                            } else if (answered == 1) {
                                $("#frage").html("Frage bereits beantwortet");
                                $("#timer_user").html("");
                            }

                            //$("#timer_user").html(counter + " Sekunden übrig");

                            counter = counter - 1;

                            //Funktion neu aufrufen
                            setTimeout(function () {
                                ajaxUser();
                            }, 1000);
                        } else {
                            state = 0;
                            $("#frage").html("");
                            $("#antworten").html("");
                            $("#timer_user").html("");
                            setTimeout(function () {
                                ajaxUser();
                            }, 1000);
                        }
                    }
                });
            });

            $(function duringQuestion(timeout) {});
        })();
    }

    //Umfrage durch Dozenten aktivieren
    if ($_POST !== null && typeof $_POST['aktivieren'] !== 'undefined') {
        if ($_POST['state'] == 0) {
            var counter;

            (function () {
                counter = $_POST['timer'];

                $('#chart_popup').popup('show');

                var interval_id = setInterval(function () {
                    if (counter > 0) {
                        $("#timer").html(counter + " Sekunden übrig");
                        counter = counter - 1;
                    } else {
                        $("#timer").html("");
                        $("#umfrage_name_dozent").html("");

                        $.ajax({
                            type: "GET",
                            url: './php/ajax.php?umfrage_id=' + $_POST['aktivieren'],
                            success: function success(result) {
                                var ergebnis = jQuery.parseJSON(result);

                                var title = ergebnis[0].frage;
                                var type = void 0;
                                var datapoints = [];

                                if (ergebnis[0].visualisierung == 0) {
                                    type = "column";
                                    $.each(ergebnis, function (index, value) {
                                        datapoints.push({ label: value.antwort, y: parseInt(value.anzahl) });
                                    });
                                } else if (ergebnis[0].visualisierung == 1) {
                                    type = "pie";
                                    $.each(ergebnis, function (index, value) {
                                        datapoints.push({ indexLabel: value.antwort, y: parseInt(value.anzahl) });
                                    });
                                }
                                var chart = new CanvasJS.Chart("chartContainer", {
                                    title: {
                                        text: title
                                    },
                                    legend: {
                                        maxWidth: 350,
                                        itemWidth: 120
                                    },
                                    data: [{
                                        type: type,
                                        dataPoints: datapoints
                                    }]
                                });
                                if (ergebnis[0].visualisierung == 1) {

                                    chart.options.data[0].showInLegend = true;
                                    chart.options.data[0].legendText = "{indexLabel}";
                                }

                                chart.render();
                            }
                        });
                        clearInterval(interval_id);
                    }
                }, 1000);
            })();
        }
    }
});

//Antwortfeld hinzufügen
function addAntwort() {
    var awCountAct = parseInt(document.getElementById("awCount").value);
    var neueAntwort = document.createElement("input");

    var awCountNew = awCountAct + 1;

    neueAntwort.name = 'aw' + awCountNew;
    neueAntwort.id = 'aw' + awCountNew;
    neueAntwort.type = "input";

    var label = document.createElement("label");
    label.innerHTML = 'Antwort ' + awCountNew + ': ';

    var container = document.createElement("div");
    container.className = "input_container_pop";
    container.appendChild(label);
    container.appendChild(neueAntwort);

    insertAfter(container, document.getElementById('aw' + awCountAct).parentNode);
    //neueAntwort.parentNode.insertBefore(label, neueAntwort);

    document.getElementById("awCount").setAttribute("value", awCountNew);
}

//Antwortfeld im Bearbeiten-Modus hinzufügen
function addEditAntwort() {
    var awCountAct = parseInt(document.getElementById("awCount_edit").value);
    var neueAntwort = document.createElement("input");

    var awCountNew = awCountAct + 1;

    neueAntwort.name = 'aw_' + awCountNew + '_edit';
    neueAntwort.id = 'aw_' + awCountNew + '_edit';
    neueAntwort.type = "text";

    var label = document.createElement("label");
    label.innerHTML = 'Antwort ' + awCountNew + ': ';

    var container = document.createElement("div");
    container.className = "input_container_pop";
    container.appendChild(label);
    container.appendChild(neueAntwort);

    insertAfter(container, document.getElementById('aw_' + awCountAct + '_edit').parentNode);
    //neueAntwort.parentNode.insertBefore(label, neueAntwort);

    document.getElementById("awCount_edit").setAttribute("value", awCountNew);
}

//Element nach einem anderen Einfügen
//FREMDE FUNKTION
function insertAfter(newElement, targetElement) {
    //target is what you want it to go after. Look for this elements parent.
    var parent = targetElement.parentNode;

    //if the parents lastchild is the targetElement...
    if (parent.lastchild == targetElement) {
        //add the newElement after the target element.
        parent.appendChild(newElement);
    } else {
        // else the target has siblings, insert the new element between the target and it's next sibling.
        parent.insertBefore(newElement, targetElement.nextSibling);
    }
}

//Cookie setzen
//FREMDE FUNKTION
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    var expires = 'expires=' + d.toUTCString();
    document.cookie = cname + '=' + cvalue + '; ' + expires + '; domain=localhost';
}

//Cookie Inhalt auslesen
//FREMDE FUNKTION
function getCookie(cname) {
    var name = cname + '=';
    var ca = document.cookie.split(';');

    var _iteratorNormalCompletion = true;
    var _didIteratorError = false;
    var _iteratorError = undefined;

    try {
        for (var _iterator = ca[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var c = _step.value;

            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally {
        try {
            if (!_iteratorNormalCompletion && _iterator.return) {
                _iterator.return();
            }
        } finally {
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }

    return "";
}