let COUNT_DAY_QUERY = 1;

jQuery(function($) {
    class Currency
    {
        constructor(elem) {
            this.action = 'currency_query';
            this.currency_first = $('#currency_first').val();
            this.currency_second = $('#currency_second').val();
            eval('this.' + $(elem).attr('id') + '=' + $(elem).val());
            this.diff = COUNT_DAY_QUERY;
        }

        query(pclear = false) {
            if(!this.action || !this.currency_first || !this.currency_second) return;
            $.ajax({
                url: window.wp_data.ajax_url,
                type: 'POST',
                data: this,
                dataType: 'json',
                success: function (response) {
                    let graph = new Graph(pclear);
                    if(!response || !response.data) {
                        alert('Нет ответа');
                        return;
                    }
                    if(response.data.error) {
                        alert('ОШИБКА. ' + response.data.error);
                        return;
                    }
                    if(!response.data.graph || !Array.isArray(response.data.graph) || response.data.graph.length == 0) {
                        alert('Не найден график');
                        return;
                    }
                    let list_graph = [];
                    for(let i in response.data.graph) {
                        list_graph.push({ x: new Date(response.data.graph[i].date_query), y: response.data.graph[i].value});
                    }
                    graph.view(list_graph);

                    if(response.data.currency1_value && response.data.currency2_value) {
                        $('#currency_first_value').val(response.data.currency1_value);
                        $('#currency_second_value').val(response.data.currency2_value);
                    }
                }
            });
        }

        convert(date_start = '', date_end = '') {

        }
    }

    class Graph
    {
        constructor(pclear = false) {
            $('#graph_currency').remove();
            $('#graph_container').removeAttr('style');
            this.pclear = pclear;
            if(pclear) return;

            $('#graph_container').attr('style', 'height: 370px; width: 100%;');
            $('#graph_container').append('<div id="graph_currency" style="height: 370px; width: 100%;"></div>');
        }

        view(data) {
            if(this.pclear) return;

            let options = {
                animationEnabled: true,
                axisX: {
                    valueFormatString: "DD.MM.YYYY",
                },
                data: [{
                    type: "area",
                    markerSize: 5,
                    xValueFormatString: "DD.MM.YYYY H:m:s",
                    dataPoints: data
                }]
            };
            $("#graph_container").CanvasJSChart(options);

        }
    }

    $(document).ready(function () {
        // Устанавливаем обработчики
        $('#currency_first').on('change', changeCurrency);
        $('#currency_first_value').on('change', changeCurrency);
        $('#currency_second').on('change', changeCurrency);
        $('#currency_second_value').on('change', changeCurrency);
        $('#count_days_1').on('click', changeCountDay);
        $('#count_days_7').on('click', changeCountDay);
        $('#count_days_30').on('click', changeCountDay);
    });

    /**
     * Обработчик выбора элемента в списке
     */
    function changeCurrency() {
        // Определяем второй список
        let selector_name = '';

        let id = $(this).attr('id');
        id = id.replace('_value', '');
        switch (id) {
            case 'currency_first':
                selector_name = 'currency_second';
                break;
            case 'currency_second':
                selector_name = 'currency_first';
                break;
            default:
                return;
        }

        // Ранее отключенные пункты включаем
        $('#' + selector_name + ' option:disabled').each(function() {
            if($(this).val() == '') return;
            $(this).removeAttr('disabled');
        });

        // Если выбрали схожий пункт, то сбрасываем второй список
        if($(this).val() && $(this).val() == $('#' + selector_name).val()) {
            $('#' + selector_name).val(null).change();
        }

        // Если выбрали значение, то отключаем такой же пункт во втором списке
        if($(this).val()) {
            $('#' + selector_name + ' option[value=' + $(this).val() + ']').attr('disabled', true);
        }

        // Грузим данные
        load(this, !$(this).val() || !$('#' + selector_name).val());
    }

    /**
     * Выбор количества дней
     */
    function changeCountDay() {
        let day = $(this).attr('data-value');
        let act_day = $('.day-active');
        $(act_day).removeClass('day-active');
        $(this).addClass('day-active');
        COUNT_DAY_QUERY = day ?? 1;
        $('#currency_first').change();
    }

    /**
     * Загрузка данных
     */
    function load(elem, pclear = false) {
        let curr = new Currency(elem);
        curr.query(pclear);
    }
});