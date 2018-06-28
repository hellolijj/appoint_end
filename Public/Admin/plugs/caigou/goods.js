/**
 * Created by Administrator on 2018/1/19 0019.
 */



/**
 * 加减数量
 * n 点击一次要改变多少
 * maxnum 允许的最大数量(库存)
 * number ，input的id
 */


function goodsnum(event){
    event.stopPropagation();
    var _this= $(this), action  = $(this).parent('.minus-plus').data('action')||'';
    var number_model  = $(this).parent().find('input.buyNum');
    var n = parseInt($(this).data('goodsnum'));
    var num = parseInt(number_model.val())||'';
    var maxnum = parseInt(number_model.attr('max'));

    num += n;
    num <= 0 ? num = 1 :  num;
    if(num >= maxnum){
        $(this).addClass('no-mins');
        num = maxnum;
    }

    if(action){
        //更新后台程序
        $.form.load(action,{num:num},'POST',function(res){
            if(res.code ){
                number_model.val(num);
                var cartTotal = $('#cartTotal'), price = parseFloat(_this.parents('td').prev('td').text()), c_total =  parseFloat( cartTotal.text() );
                var total = ( price * num );
                _this.parents('td').next('td').text( total .toFixed(2));
                cart_Total();
            }else{
                $.msg.auto(res, 3);
            }
            return false;
        });

    }
}

var cart_Total = function(){
    var total = 0,cartTotal = $('#cartTotal');
        $('table.table_cart tbody tr').each(function(){
           var checkbox =  $(this).find('input[type=checkbox].goods');
            if(checkbox.prop('checked')){
                total +=  parseFloat( checkbox.parents('tr').find('td.total').text() ) || 0;
            }
        });
    cartTotal.text( total .toFixed(2) );
};

$('body').on('click','[data-add_cart]',function(){
    var action= $(this).data('add_cart'), cart = $('[data-cartnum]'),num = parseInt(cart.data('cartnum')),addNum = parseInt($('#number').val())||1;
    var Total = num + addNum;
    $.form.load(action,{num:addNum},'POST',function(res){
        if(res.code ){
            cart.data('cartnum',Total).text(Total);
            $.msg.tips(res.msg,2);
        }else{
            $.msg.auto(res, 3);
        }
        return false;
    });
}).on('click','[data-goodsnum]',goodsnum).on('blur','[data-goodsnum].buyNum',goodsnum).on('click','input[type=checkbox]',cart_Total);
