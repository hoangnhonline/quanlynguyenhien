class TaskHelper {
    //function to remove query params from a URL
    static removeURLParameter(url, parameter) {
        //better to use l.search if you have a location/link object
        var urlparts = url.split("?");
        if (urlparts.length >= 2) {
            var prefix = encodeURIComponent(parameter) + "=";
            var pars = urlparts[1].split(/[&;]/g);

            //reverse iteration as may be destructive
            for (var i = pars.length; i-- > 0;) {
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }

            url = urlparts[0] + (pars.length > 0 ? "?" + pars.join("&") : "");
            return url;
        } else {
            return url;
        }
    }

    //function to add/update query params
    static insertParam(key, value) {
        if (history.pushState) {
            // var newurl = window.location.protocol + "//" + window.location.host + search.pathname + '?myNewUrlQuery=1';
            var currentUrlWithOutHash =
                window.location.origin +
                window.location.pathname +
                window.location.search;
            var hash = window.location.hash;
            //remove any param for the same key
            var currentUrlWithOutHash = this.removeURLParameter(
                currentUrlWithOutHash,
                key
            );

            //figure out if we need to add the param with a ? or a &
            var queryStart;
            if (currentUrlWithOutHash.indexOf("?") !== -1) {
                queryStart = "&";
            } else {
                queryStart = "?";
            }

            var newurl =
                currentUrlWithOutHash + queryStart + key + "=" + value + hash;
            window.history.pushState(
                {
                    path: newurl,
                },
                "",
                newurl
            );
        }
    }

    static refreshList() {
        const urlParams = new URLSearchParams(window.location.search);
        $.ajax({
            url: "/task?" + urlParams.toString(),
            type: "GET",
            success: function (res) {
                $(".task-list-row").html(res);
                TaskHelper.init();
            },
        });
    }

    static refreshLogList(taskId) {
        $.ajax({
            url: `/task/${taskId}/logs`,
            type: "GET",
            success: function (res) {
                $(".activities-list-area .activities").html(res);
            },
        });
    }

    static reloadScript() {
        $(document).find(".select2").select2({
            theme: "bootstrap4",
        });

        $(document).find(".datepicker").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+2",
            clearBtn: true,
        });
        // $(document).find(".datetimepicker").datetimepicker({
        //     sideBySide: true,
        //     format: "DD/MM/YYYY HH:mm",
        //     // container: "body",
        // });
        $(document).find(".datetimepicker").datetimepicker({
            format: "d/m/Y H:i",
            step: 15,
        });
        if ($('#description').length == 1) {
            const editor = CKEDITOR.instances["description"]
            if (editor) { editor.destroy(true); }
            setTimeout(() => {
                CKEDITOR.replace('description', {
                    height: 300
                });
            }, 0)
        }
    }

    static handleForm(field) {
        const {name, value} = field;
        this.insertParam(name, value);
    }

    static init() {
        $(".task-list").sortable({
            cursor: "move",
            connectWith: ".task-list",
            items: ".task-card",
            appendTo: ".adminV2",
            helper: "clone",
            receive: function (event, ui) {
                const taskId = $(ui.item).data("task-id");
                const status = $(ui.item).closest(".task-list").data("status");

                $.ajax({
                    url: `task/${taskId}/status`,
                    type: "POST",
                    data: {
                        status,
                    },
                    success: function (res) {
                        TaskHelper.refreshList()
                        TaskHelper.displayNotification('#notification', res.message)
                    },
                    error: function () {
                        window.location.reload();
                    }
                });
            },
        });

        $(document).find(".select2").select2({
            theme: "bootstrap4",
        });

        const urlParams = new URLSearchParams(window.location.search);
        const toDate = moment(urlParams.get("to_date"), "MM/DD/YYYY", true);
        const fromDate = moment(urlParams.get("from_date"), "MM/DD/YYYY", true);

        $(document)
            .find(".from-datepicker")
            .datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+2",
                maxDate: toDate.isValid() ? toDate.toDate() : undefined,
                onSelect: function (dateStr) {
                    $(".to-datepicker").val("");
                    $(".to-datepicker").datepicker("option", {
                        minDate: new Date(dateStr),
                    });
                    $("#filterForm").trigger("submit");
                },
            });
        $(document)
            .find(".to-datepicker")
            .datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+2",
                minDate: fromDate.isValid() ? fromDate.toDate() : undefined,
                onSelect: function (dateStr) {
                    $(".from-datepicker").datepicker("option", {
                        maxDate: new Date(dateStr),
                    });
                    $("#filterForm").trigger("submit");
                },
            });
    }

    static displayNotification(el, message, type = "info") {
        $(el).html(`
            <p class="alert alert-${type} alert-dismissible" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="icon-close"></i>
                </button>
            </p>`);
    }

    static getTask(taskId, workInforModal) {
        $.ajax({
            url: "task/" + taskId,
            type: "GET",
            success: function (res) {
                workInforModal.find(".modal-content").html(res);
                workInforModal.modal("show").addClass("show");
                $(".tooltip").tooltip();
                $('[data-toggle="popover"]').popover({
                    trigger: "focus",
                    html: true
                });
            },
            error: function (res) {
                if (res.status === 403) {
                    TaskHelper.displayNotification('#notification', "Không có quyền truy cập!", 'error')
                }
            }
        });
    }

    static convertDotToSquare(name) {
        return name.replace(/\.(.+?)(?=\.|$)/g, (m, s) => `[${s}]`);
    }

    static changeTodoIndexInputName(name, index) {
        return name.replace(/data\[\d+\]/, `data[${index}]`);
    }
}
