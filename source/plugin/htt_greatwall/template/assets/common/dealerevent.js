$(function(){
	//初始化 车型 省 市 经销商 并联动
	bindCar();
	$('#v-car').change(function () {
        now_car = $(this).val();
        bindProvince();
        $('#v-province').change();
    }).change();
	$('#v-province').change(function () {
        bindCity();
        $('#v-city').change();
    });
    $('#v-city').change(function () {
        bindDealer();
        $('#v-dealer').change();
    });
});
var now_car='';
function bindCar() {
    var car = [['hf7','哈弗H7'],['hf6','哈弗H6Coupe1.5T']];
    var str = "";//"<option value=\"\">" + "请选择" + "</option>";
    for (var i = 0; i < car.length; i++) {
        str += "<option value=\"" + car[i][0] + "\">" + car[i][1] + "</option>";
    }
    $('#v-car').html(str);
    now_car = $('#v-car').val();
}
function bindProvince() {
    if (JSonData) {
        var str = "<option value=\"\">" + "请选择" + "</option>";
        for (var i = 0; i < JSonData[now_car].length; i++) {
            if (str.indexOf(JSonData[now_car][i].pro) < 0) {
                str += "<option value=\"" + JSonData[now_car][i].pro + "\">" + JSonData[now_car][i].pro + "</option>";
            }
        }
        $('#v-province').html(str);
    }
}
function bindCity() {
    var province = $('#v-province').find("option:selected").text();
    var str = "<option value=\"\">" + "请选择" + "</option>";
    for (var i = 0; i < JSonData[now_car].length; i++) {
        if (JSonData[now_car][i].pro == province) {
            if (str.indexOf(JSonData[now_car][i].city) < 0) {
                str += "<option value=\"" + JSonData[now_car][i].city + "\">" + JSonData[now_car][i].city + "</option>";
            }
        }
    }
    $('#v-city').html(str);
}
function bindDealer() {
    var str = "<option value=\"\">" + "请选择" + "</option>";
    var city = $('#v-city').find("option:selected").text();
    for (var i = 0; i < JSonData[now_car].length; i++) {
        if (JSonData[now_car][i].city == city) {
            str += "<option value=\"" + JSonData[now_car][i].dealer+ "\">" + JSonData[now_car][i].dealer + "</option>";
        }
    }
    $('#v-dealer').html(str);
}