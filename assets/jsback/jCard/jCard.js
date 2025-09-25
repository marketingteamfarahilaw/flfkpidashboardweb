$.jCard = {};
$.jCard.gridData = [];
$.jCard.manipulateGrid = function(param) {
    var index = param.index;
    delete param.index;

    $.extend($.jCard.gridData[index], param);

    $.jCard.generateGrid($.jCard.gridData[index], index);
};

$.jCard.newCard = function(param) {
    var index = $.jCard.gridData.length;

    $.jCard.gridData[index] = $.extend({
        /* TRUE, Last button in pagination will not showing */
        hideLastButton: false,

        /* TRUE, First button must will not showing */
        hideFirstButton: false,

        /* TRUE, number segments in pagination will not showing */
        hideSegments: false,

        /* TRUE, top pagination must show */
        topBar : true,

        /* TRUE, bottom pagination must show */
        bottomBar : true,

        /* Closing element */
        opening : '',

        /* Closing element */
        closing : '',

        /* Target element where table must be rendered */
        target : '',

        /* Form Tag where search input is located */
        searchData : '',

        /* Url that ajax is requesting */
        url : '',

        /* List of column in table */
        columns : {},

        /* TRUE, list count in first column must shown */
        count : true,

        /* Table name that will be sorted (ORDER BY Clause)*/
        sort : '',

        /* Sort orderly Asc/Desc (ORDER BY Clause) */
        order : '',

        /* Offset of list (LIMIT Clause) */
        offset : 0,

        /* LIMIT of list (LIMIT Clause) */
        limit : 30,

        /* Label of Previous button */
        previousLabel : 'Previous',

        /* Label of Next button */
        nextLabel : 'Next',

        /* Label of First button */
        firstLabel : 'First',

        /* Label of Last button */
        lastLabel : 'Last',

        /* Loading image */
        loading : '<div class="jCard-ajax-loading"><div><span></span><label>Loading</label></div></div>',

        /* Execute if rendiring is complete */
        complete : function(){},

        /* data that will be included by submitting form */
        includeData : {}

    }, param);

    $(param.target).append($.jCard.gridData[index].loading);

    $.jCard.generateGrid($.jCard.gridData[index], index);
};

$.jCard.generateGrid = function(opt, index) {
    var dData = [];

    ajaxRequest();

    function setPostData() {
        searchData  = '';
        searchData += $.param({
                          offset : typeof opt.offset2 != 'undefined' ? opt.offset2 : opt.offset,
                          limit : typeof opt.limit2 != 'undefined' ? opt.limit2 : opt.limit,
                          sort : typeof opt.sort2 != 'undefined' ? opt.sort2 : opt.sort,
                          order : typeof opt.order2 != 'undefined' ? opt.order2 : opt.order,
                      });

        var includeData = $.param(opt.includeData);

        if(includeData != '') {
            searchData += '&'+ includeData;
        }

        if(opt.searchData != '') {
            searchData += '&'+ $(opt.searchData).serialize();
        }

        return searchData;
    }

    function ajaxRequest() {
        $.ajax({
            url : opt.url,
            data : setPostData(),
            type : 'post',
            dataType : 'json',
            beforeSend: function(xhr) {
                xhr.setRequestHeader ("Authorization", "Basic " + btoa('webPortal' + ":" + 'VV313p0Rt@l'));
                $(opt.target +' div[data-jCard]').append(opt.loading);
            },
            success: function(r) {
                dData = r;
                $(opt.target).html(generate()).promise().done(function(){
                    opt.complete();
                });
            }
        });
    }

    function generate() {
        e = get_card();

        b = opt.topBar == true || opt.bottomBar == true ? barControl() : '';

        return opt.opening
                + '<div class="row" data-jCard>'
                + (opt.topBar == true ? b : '')
                + e
                + '</div>'
                + (opt.bottomBar == true ? b : '')
                + opt.closing;
    }

    function barControl() {
        if(dData.total <= opt.limit) return '';

        /* METHOD variables */
        var c = '',                                 /* string contains of html/pagination tags */
            offs,                                   /* offset value that will be suppied to the button fist/next/previos/last */
            limit = opt.limit,
            offset = opt.offset;

        /* PAGINATION variables */
        var cSegment = (offset / limit) + 1,        /* current segment */
            counter = 1,                            /* limiting the number of segments in pagination */
            p = '<label>'+ cSegment +'</label>',    /* string contains of html/pagination segments */
            numSegment = 5;

        c  = '<div id="jCard-action-bar"><span>';

        /* First button */
        if(cSegment-1 > numSegment && opt.hideFirstButton == false) {
            c += "<button class=\"jCard-btn\" title=\"Next\" ";
            c += "onclick=\"$.jCard.manipulateGrid({index:'"+index+"',offset:'0'})\">";
            c += opt.firstLabel +"</button>";
        }

        /* Previous button */
        if((offs = parseFloat(offset) - parseFloat(limit)) >= 0) {
            c += "<button class=\"jCard-btn\" title=\"Previous\" ";
            c += "onclick=\"$.jCard.manipulateGrid({index:'"+index+"',offset:'"+offs+"'})\">";
            c += opt.previousLabel +"</button>";
        }

        if(opt.hideSegments == false) {
            /* PAGINATION SEGMENTS */
            do {
                // if(((cSegment - 1) + counter) * limit <= dData.total)
                if(((cSegment - 1) + counter) * limit < dData.total)
                {
                    p += "<button onclick=\"$.jCard.manipulateGrid({index:'"+index+"',";
                    p += "offset:"+(((cSegment - 1) + counter) * limit)+"})\">"+(cSegment + counter)+'</button>';
                }

                if(((cSegment - 1) - counter) * limit >= 0)
                {
                    p = "offset:"+(((cSegment - 1) - counter) * limit)+"})\">"+(cSegment - counter)+'</button>' + p;
                    p = "<button onclick=\"$.jCard.manipulateGrid({index:'"+index+"'," + p;
                }

                counter++;
            }
            while(counter <= numSegment);

            c += p;
        }

        /* Next button */
        if((offs = parseFloat(offset) + parseFloat(limit)) < dData.total) {
            c += "<button class=\"jCard-btn\" title=\"Next\" ";
            c += "onclick=\"$.jCard.manipulateGrid({index:'"+index+"',";
            c += "offset:'"+offs+"'})\">";
            c += opt.nextLabel +"</button>";
        }

        /* LAST button */
        var lastOffset = dData.total % limit;
        if(Math.ceil(dData.total / limit) - (cSegment) > numSegment && opt.hideLastButton == false) {
            c += "<button class=\"jCard-btn\" title=\"Next\" ";
            c += "onclick=\"$.jCard.manipulateGrid({index:'"+index+"',";
            c += "offset:'"+(dData.total - (lastOffset == 0 ? limit : lastOffset))+"'})\">";
            c += opt.lastLabel +"</button>";
        }

        c += '</span></div>';

        return c;
    }

    function get_card(v) {
        var td = '', count = opt.offset;

        if(dData.total == 0) {
            count = opt.count == true ? 1 : 0;

            $.each(opt.columns, function(indexColumns, columns)
            {
                count++;
            });

            td += '<tr>';
            td += '<td colspan="'+ count +'" class="no-result">';
            td += '- No result -';
            td += '</td>';
            td += '</tr>';

            return td;
        }

        

        $.each(dData.data, function(indexData, data, v) {   
            var obj = data;
            var arr = $.makeArray(obj);
            var name = $.makeArray(obj['name']);
            var email = $.makeArray(obj['email']);
            var profilepicture = $.makeArray(obj['customer_image_url']);
            var id = $.makeArray(obj['id']);
            var base_url = window.location.host;
            if(base_url === "localhost"){
                var url = 'http://localhost/lawdger/';
            }
            else {
                var url = 'http://lawdger.com/beta/';
            }

            if(base_url === "localhost"){
                var img_url = 'http://localhost/lawdger-service/';
            }
            else {
                var img_url = 'http://lawdger.com/api/';
            }

            td += '<div class="col-12 col-sm-6 col-lg-4">';
                td += '<div class="card card-widget widget-user">';

                    td += '<div class="widget-user-header text-white bc-primary" >';
                    td += '</div>';

                    td += '<div class="widget-user-image">';
                        td += '<p class="widget-user-desc "></p>';
                        td += '<img class="img-circle" src="'+ img_url + 'uploads/' +profilepicture+'.png " alt="User Avatar">';
                    td += '</div>';

                    td += '<div class="card-footer p-1 pt-5">';
                        td += '<div class="pt-2 text-center">';
                            // td += '<h4 class="widget-user-username fw-4>'+profilepicture+'</h4>';
                            td += '<h4 class="widget-user-username fw-400">'+name+'</h4>';
                            td += '<p class="widget-user-desc">'+email+'</p>';
                            td += '<div class="pt-4 ">';
                                td += '<a href="compose/'+ id +'" class="mb-3 __transition green-link d-inline-block ml-auto mr-auto">Messages</a>';
                                td += '<a onclick="myFunction('+id+')" class="mb-3 __transition primary-link d-inline-block ml-auto mr-auto">Connect</a>';
                            td += '</div>';
                            
                            td += '<div class="pb-4">';
                                td += '<a href="' + url + 'profile/'+ id +'" class="__transition d-block c-black pt-2 pb-2 ml-auto mr-auto">VIEW PROFILE</a>';
                            td += '</div>';
                        td += '</div>';
                    td += '</div>'; 

            td += '</div>';
            td += '</div>';   
        });

        return  td ;
    }
};

function myFunction(id) {
    var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
    var bodyFormData = new FormData();
    bodyFormData.set("lawyer_connected", id);
    axios.post(endPoints.CONNECT.concat('/'+ id).concat('?token='+token), bodyFormData, CONFIG.HEADER)
                            .then( (response) => {
                                toastr.success('Successfully connected!');
                                setTimeout(function() { 
                                    window.location = 'compose/'+id;
                                }, 2000);
                            })
                            .catch( (error) => {
                                toastr.error(error.response);
                                console.log(error.response);
                            });
}

$(function() {
    $('*[data-jCardTarget]').on('click', function(e){
        e.preventDefault();
        var index;

        for(var i = 0; i < $.jCard.gridData.length, typeof index == 'undefined'; i++) {
            if($.jCard.gridData[i].target == $(this).attr('data-jCardTarget')) {
                index = i;
                break;
            }
        }

        $.extend($.jCard.gridData[index], {
            offset : 0,
        });

        $.jCard.generateGrid($.jCard.gridData[index], index);
    });
});