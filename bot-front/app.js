
$(function () {
    let page = 1;
    let sort_params = {
        'width':-1,
        'height':-1,
        'radius':'',
        'brand':'',
        'rft':-1,
        'spike':-1,
    }

    let renderItem = function (item,key,value) {
        item = item.replaceAll(`{{tire.${key}}}`,value)
        return item
    }

    let activeCheck = function (e) {
        $('.page').removeClass('active')
        $(e).addClass('active')
    }

    let paginator = function() {
        $.post('https://mdxv.store/lot?method=paginate',function(data){
            let active;
            let arr = Array.from({length: parseInt(data)}, (_, i) => i + 1)
            $('#demo').pagination({
                dataSource: arr,
                pageSize: 5,
                showPrevious: false,
                showNext: false,
                callback: function() {
                    loadItems()
                }
            })
        })
    }

    let render = function (items) {
        items.forEach(item => {
            let template = $('#tire').html()
            let image = (item.photo)?item.photo[1]:'';
            item.count = (item.count>20)?20:item.count
            if (image) {
                template = template.replaceAll('{{tire.image}}',image)
                Object.entries(item).filter(([key, value]) => template = renderItem(template,key,value));
                if (item.count<4) {
                    template = template.replace('{{tire.val}}',item.count)
                } else {
                    template = template.replace('{{tire.val}}',4)
                }
                $('#tires').append(template)
            }
        });
    }

    let loadItems = function () {
        $.post('https://mdxv.store/lot?method=tires',{'page':page},function (data) {
            $('#tires').html('')
            render(data)
        })
    }

    let init = function () {
        loadItems()
        $.post('https://mdxv.store/lot?method=width',function (width) {
            for (let index = 0; index < width.length; index++) {
                const w = width[index];
                $('#width').append(`<option value="${w}">${w}</option>`)
            }
        })
        $.post('https://mdxv.store/lot?method=height',function (height) {
            for (let index = 0; index < height.length; index++) {
                const h = height[index];
                $('#height').append(`<option value="${h}">${h}</option>`)
            }
        })
        $.post('https://mdxv.store/lot?method=radius',function (radius) {
            for (let index = 0; index < radius.length; index++) {
                const r = radius[index];
                $('#radius').append(`<option value="${r}">${r}</option>`)
            }
        })
        $.post('https://mdxv.store/lot?method=brand',function (brand) {
            for (let index = 0; index < brand.length; index++) {
                const b = brand[index];
                $('#brand').append(`<option value="${b}">${b}</option>`)
            }
        })
        $('#paginate').html('')
        paginator()
    }

    init()
})

$('#winter').click(function () {
    $('#summer').prop('checked',false)
})
$('#summer').click(function () {
    $('#winter').prop('checked',false)
})
$('#spike').click(function () {
    $('#unspike').prop('checked',false)
})
$('#unspike').click(function () {
    $('#spike').prop('checked',false)
})

$('#filters').click(function () {
    if ($('.show-sort').length) {
        $('#sort').removeClass('show-sort')
    }else{
        $('#sort').addClass('show-sort')
    }
})

function plus(e) {
    let el = $(e).parent().find('.quantity');
    if ($(el).val()<parseInt($(e).data('max'))) {
        $(el).val(parseInt($(el).val())+1)
    } else {
        $(el).val(parseInt($(e).data('max')))
    }
    
}

function minus(e) {
    let el = $(e).parent().find('.quantity');
    if ($(el).val()>1) {
        $(el).val(parseInt($(el).val())-1)
    } else {
        $(el).val(1)
    }
}

