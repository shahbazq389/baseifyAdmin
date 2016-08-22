

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

        var jsver=$("#group-by").val();

		Metronic.startPageLoading();
		jQuery.ajax({
            url: url_analytics_hourly_jsver_ajax,
            type: "post",
            dataType : 'json',
            data: {'date_start':jQuery('input[name=analytics_order_date_start]').val(),'jsver':jsver,'_token': jQuery('input[name=_token]').val()},
            success: function(data){
                var generate_list="";


                if(data[0].length==0){
                    generate_list='<tr><td colspan="3" class="text-center"><span class="text-danger">No record found</span></td></tr>'
                    $(".graph-no-data-found").show();
                    $(".graph-show-data").hide();

                }
                else {
                    for (var i = 0; i < data[0].length; i++) {
                        //var generate_data = data[key];
                        generate_list += "<tr class='list_tr_"+i+"'>" +
                            "<td>" + data[0][i].hour_range + "</td>" +
                            "<td>" + data[0][i].total_jsver + "</td>" +
                            "<td>" + data[0][i].total_clicks + "</td>" +
                           // "<td>" + data[i].total_searches + "</td>" +
                            "</tr>";
                        //console.log(generate_list);

                    }
                     //var dataProvider=array();


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
                        //"dataDateFormat": "HH",

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
                        "graphs": data[1],
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
                        "categoryField": "hour_range",
                        "categoryAxis": {
                            "parseDates":false,
                            "dashLength": 1,
                            "labelRotation": 90,
                            "minorGridEnabled": true,

                        },
                        "export": {
                            "enabled": true,

                        },
                        "dataProvider":data[0]
                    });


                    $('#chart_1').closest('.portlet').find('.fullscreen').click(function() {
                        chart.invalidateSize();
                    });



                    $(".graph-show-data").show();
                    $(".graph-no-data-found").hide();
                    generate_list+='<tr><td><b>Total</b></td><td></td>td><td><b>'+data['0'][0]['total_sum_clicks']+'</b></td></tr>';

                }

                   /*for(key in data){
                     var temp=data[key];

                   }*/
                //alert(data['0']['total_sum_clicks'])


                jQuery("tbody").html(generate_list);
                Metronic.stopPageLoading();
            }
        });

    };

    initPickers();

    jQuery('.btn_hourly_ajax').click(function(){
	    getData();

    });
});