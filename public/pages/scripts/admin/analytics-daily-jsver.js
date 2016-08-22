
jQuery(document).ready(function(){

    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            format: 'mm-dd-yyyy',
            rtl: Metronic.isRTL(),
            autoclose: true
        });

        $(".date-picker").each(function() {
            $(this).datepicker('update', new Date());
            $(this).datepicker('update');
        });

        getData();

    };

    var getData=function(){
        var chk='';
        if($("input[name=widget]").is(':checked')){
            var chk=$("input[name=widget]").is(':checked')

        }
        else{

            var chk=$("input[name=widget]").is(':checked')


        }

        Metronic.startPageLoading();
        var jsver=$("#group-by").val();
        //alert(widget);
        jQuery.ajax({
            url: url_analytics_daily_jsver_ajax,
            type: "post",
            dataType : 'json',
            data: {'date_start':jQuery('input[name=analytics_order_date_start]').val(),'date_end':jQuery('input[name=analytics_order_date_end]').val(), 'jsver':jsver,  '_token': jQuery('input[name=_token]').val()},
            success: function(data){
                var generate_list="";
                var thead="";
                if(data[0].length==0){
                    generate_list='<tr><td colspan="4" class="text-center"><span class="text-danger">No record found</span></td></tr>'
                    $(".graph-no-data-found").show();
                    $(".graph-show-data").hide();
                    $(".daily-jsver-show-graph").hide();

                }

                else {
                    //var i=0;
                    //alert(data[0].total_widget);
                    for (i = 0; i < data[0].length; i++) {

                        //var generate_data = data[key];
                        var thead = '<tr><td>Date</td><td>Widget</td><td>Total Clicks</td><td>Total Searches</td></tr>'
                            generate_list += "<tr>" +
                                "<td>" + data[0][i].dates + "</td>" +
                                "<td>" + data[0][i].total_jsver + "</td>" +
                                "<td>" + data[0][i].total_clicks + "</td>" +
                                //"<td>" + data[0][i].total_searches + "</td>" +
                                "</tr>";

                            if (i + 1 == data[0].length) {

                                generate_list += '<tr><td><b>Total</b></td><td></td><td><b>' + data['0']['0']['total_sum_clicks'] + '</b></td></tr>';

                            }

                    }
                    // var thead = '<tr><td>Date</td><td>Widget</td><td>Total Clicks</td><td>Total Searches</td></tr>'

                    var chart = AmCharts.makeChart("chart_1", {
                        "type": "serial",
                        "theme": "light",
                        "legend": {
                            "horizontalGap": 10,
                            "maxColumns": 1,
                            "position": "right",
                            "useGraphSettings": true,
                            "markerSize": 10
                        },
                        "marginRight": 40,
                        "marginLeft": 40,
                        "autoMarginOffset": 20,
                        "mouseWheelZoomEnabled":false,
                        "dataDateFormat": "YYYY-MM-DD",
                        "valueAxes": [{
                            "id": "v1",
                            "axisAlpha": 0,
                            "position": "left",
                            // "ignoreAxisWidth":true,
                            "title":"Clicks"
                        }],
                        "balloon": {
                            "borderThickness": 1,
                            "shadowAlpha": 0
                        },
                        "graphs": data[2],
                        "chartCursor": {
                            "pan": true,
                            "valueLineEnabled": true,
                            "valueLineBalloonEnabled": true,
                            "cursorAlpha":1,
                            "cursorColor":"#258cbb",
                            "limitToGraph":"g1",
                            "valueLineAlpha":0.2,
                            "valueZoomable":true
                        },
                        "valueScrollbar":{
                            "oppositeAxis":false,
                            "offset":50,
                            "scrollbarHeight":10
                        },
                        "categoryField": "dates",
                        "categoryAxis": {
                            "parseDates": true,
                            "dashLength": 1,
                            "minorGridEnabled": true
                        },
                        "export": {
                            "enabled": true
                        },
                        "dataProvider":data[0]
                    });


                    $('#chart_1').closest('.portlet').find('.fullscreen').click(function() {
                        chart.invalidateSize();
                    });
                   /* var chart = AmCharts.makeChart("chart_2", {
                        "type": "serial",
                        "theme": "light",
                        "legend": {
                            "horizontalGap": 10,
                            "maxColumns": 1,
                            "position": "right",
                            "useGraphSettings": true,
                            "markerSize": 10
                        },
                        "marginRight": 40,
                        "marginLeft": 40,
                        "autoMarginOffset": 20,
                        "mouseWheelZoomEnabled":false,
                        "dataDateFormat": "YYYY-MM-DD",
                        "valueAxes": [{
                            "id": "v1",
                            "axisAlpha": 0,
                            "position": "left",
                            // "ignoreAxisWidth":true,
                            "title":"Clicks and Searches"
                        }],
                        "balloon": {
                            "borderThickness": 1,
                            "shadowAlpha": 0
                        },
                        "graphs": data[3],

                        "chartCursor": {
                            "pan": true,
                            "valueLineEnabled": true,
                            "valueLineBalloonEnabled": true,
                            "cursorAlpha":1,
                            "cursorColor":"#258cbb",
                            "limitToGraph":"g1",
                            "valueLineAlpha":0.2,
                            "valueZoomable":true
                        },
                        "valueScrollbar":{
                            "oppositeAxis":false,
                            "offset":50,
                            "scrollbarHeight":10
                        },
                        "categoryField": "dates",
                        "categoryAxis": {
                            "parseDates": true,
                            "dashLength": 1,
                            "minorGridEnabled": true
                        },
                        "export": {
                            "enabled": true
                        },
                        "dataProvider":data[1]
                    });

                    chart.addListener("rendered", zoomChart);

                    zoomChart();

                    function zoomChart() {
                        chart.zoomToIndexes(chart.dataProvider.length - 40, chart.dataProvider.length - 1);
                    }

                    $('#chart_2').closest('.portlet').find('.fullscreen').click(function() {
                        chart.invalidateSize();
                    });*/

                        $(".graph-no-data-found").hide();
                        $(".graph-show-data").show();

                        /*for(key in data){

                         var temp=data[key];

                         }*/
                        //jQuery("thead").html(thead);

                        //generate_list+='<tr><td><b>Total</b></td><td><b>'+data['0']['total_sum_clicks']+'</b></td><td><b>'+data['0']['total_sum_searches']+'</b></td></tr>';

                }


                jQuery("tbody").html(generate_list);
                Metronic.stopPageLoading();
            }
        });

    };

    initPickers();

    jQuery('.btn_daily_ajax').click(function(){


        getData();

    });
});