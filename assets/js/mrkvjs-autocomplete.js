// Widget 'jquery-ui-autocomplete' on 'Налаштування' page 'Відправка з:' block
jQuery(document).ready(function() {
    var term = jQuery('#woocommerce_morkvajustin_shipping_method_city_name').val();
    var cityInputName = jQuery('#woocommerce_morkvajustin_shipping_method_city_name');
    var cityInputUuid = jQuery('#woocommerce_morkvajustin_shipping_method_city');

    var allCitiesObj = [];
    var citySelectedUuid = '';

    // City
    if (typeof cityInputName.autocomplete !== "undefined") { // Get city names by jQuery-ui autocomplete
        cityInputName.autocomplete({
            minLength: 2,
            delay: 500, // default = 300
            source: function(request, response){
                jQuery.ajax({
                    type: 'POST',
                    url: MrkvjsAutocompleteSearch.ajax_url,
                    data: {
                        action: 'mrkvjs_autocomplete_cities',
                        name: request.term,
                    },
                    success: function (res) {
                        allCitiesObj = JSON.parse(MrkvjsAutocompleteSearch.mrkvjs_city_names);
                        var arrCityNames = [];
                        for (i = 0; i < allCitiesObj.length; i++){
                            arrCityNames.push(allCitiesObj[i].descr);
                        }

                        var filterCities = function(){
                            var searchLine = cityInputName.val(); // Get current entered string from 'Місто' input field
                            // Make first letter from 'Місто' input field uppercase
                            searchLine = searchLine.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
                                return letter.toUpperCase();
                            });
                            // jQuery.grep loops through each city names array item and return 'true' to include the item or
                            // false to exclude that item from new 'include_cities' array
                            var filtered_cities = jQuery.grep(arrCityNames, function(item, index){
                                var include_cities = item.includes(searchLine,0);
                                return include_cities;
                            });
                            return filtered_cities;
                        }

                        response(filterCities());
                    },
                    error: function(errorThrown){
                        alert(errorThrown.statusText);
                        console.log('error: ');console.log(errorThrown);
                    }
                }) // jQuery.ajax
            }, // source: function(request, response)
            focus: function (event, ui) {
                cityInputName.val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                var citySelectedName = ui.item.value;
                console.log('citySelectedName');console.log(ui.item.value);
                for (i = 0; i < allCitiesObj.length; i++){
                    var cityObj = allCitiesObj[i];
                    if (citySelectedName == cityObj.descr) {
                        citySelectedUuid = cityObj.uuid;
                    }
                }
                console.log('citySelectedUuid');console.log(citySelectedUuid);
                cityInputUuid.val(citySelectedUuid);
                return false;
            }
        }); // cityInputName.autocomplete
    } else {
        console.log('autocomplete undefined!');
    } // if (typeof cityInputName.autocomplete !== "undefined")


    var warehouseInputName = jQuery('#woocommerce_morkvajustin_shipping_method_warehouse_name');
    var warehouseInputUuid = jQuery('#woocommerce_morkvajustin_shipping_method_warehouse');
    // var citySelectedUuid = jQuery('#woocommerce_morkvajustin_shipping_method_city').val();

    var allWarehousesObj = [];
    var warehouseCityUuids = [];
    var warehouseSelectedUuid = '';

    // Warehouse
    if (typeof warehouseInputName.autocomplete !== "undefined") { // Get warehouse names by jQuery-ui autocomplete
        warehouseInputName.autocomplete({
            minLength: 0,
            delay: 500, // default = 300
            source: function(request, response){
                jQuery.ajax({
                    type: 'POST',
                    url: MrkvjsAutocompleteDpts.ajax_url,
                    data: {
                        action: 'mrkvjs_autocomplete_city_warehouses',
                        name: request.term,
                        security: MrkvjsAutocompleteSearch.mrkvjs_nonce
                    },
                    success: function (res) {
                        var allWarehousesObj = JSON.parse(MrkvjsAutocompleteDpts.mrkvjs_warehouse_names);MrkvjsAutocompleteDpts
                        var arrWarehouseNames = [];
                        for (i = 0; i < allWarehousesObj.length; i++){
                            arrWarehouseNames.push(allWarehousesObj[i].descr);
                        }
                        var warehouseCityNames = [];
                        var filterWarehouses = function(){
                            for (i = 0; i < allWarehousesObj.length; i++){
                                var warehouseObj = allWarehousesObj[i];
                                if (citySelectedUuid == warehouseObj.city_uuid) {
                                    warehouseCityNames.push(warehouseObj.descr);
                                    warehouseCityUuids.push({'uuid':warehouseObj.uuid, 'descr':warehouseObj.descr});
                                }
                            }
                            if (!warehouseCityNames.length) {
                                warehouseCityNames = {descr:'немає відділень'};
                            }
                            console.log('warehouseCityNames');console.log(warehouseCityNames);
                            return warehouseCityNames;
                            // jQuery.grep loops through each warehouse names array item and return 'true' to include the item or
                            // false to exclude that item from new 'include_warehouses' array
                            var filtered_warehouses = jQuery.grep(arrWarehouseNames, function(item, index){
                                return include_warehouses;
                            });
                            return filtered_warehouses;
                        }
                        response(filterWarehouses());
                    },
                    error: function(errorThrown){
                        alert(errorThrown.statusText);
                        console.log('error: ');console.log(errorThrown);
                    }
                }) // jQuery.ajax
            }, // source: function(request, response)
            focus: function (event, ui) {
                warehouseInputName.val(ui.item.label);
                return false;
            },
            select: function (event, ui) {
                var warehouseSelectedName = ui.item.value;
                console.log('warehouseSelectedName');console.log(warehouseSelectedName);
                console.log('warehouseCityUuids');console.log(warehouseCityUuids);
                for (i = 0; i < warehouseCityUuids.length; i++){
                    var warehouseUuid = warehouseCityUuids[i];
                    if (warehouseSelectedName == warehouseUuid.descr) {
                        warehouseSelectedUuid = warehouseUuid.uuid;
                    }
                }
                console.log('warehouseSelectedUuid');console.log(warehouseSelectedUuid);
                warehouseInputUuid.val(warehouseSelectedUuid);
                cityInputUuid.val(citySelectedUuid);
                return false;
            }
        }); // warehouseInputName.autocomplete
    } // if (typeof warehouseInputName.autocomplete !== "undefined")

    // Alert for 'Друк стікера' button click on 'Редагувати замовлення' page
    jQuery('.mrkvjs_stickerprint').on('click', function(e) {
        att = jQuery(this).attr('alert');
        if (att != '') {
            alert(att);
        }
    });

    // Show message about success created JustIn order invoice on 'Створити накладну' page
    var mrkvjsInvoiceMsg = document.getElementById("messagebox");
    if (mrkvjsInvoiceMsg) {
        var h = mrkvjsInvoiceMsg.getAttribute('data');
        mrkvjsInvoiceMsg.style.height = h + 'px';
        mrkvjsInvoiceMsg.style.padding = 8 + 'px';
    }

}); // jQuery(document).ready(function() {
