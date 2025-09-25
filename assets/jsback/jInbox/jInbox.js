$.jMsg = {};
$.jMsg.gridData = [];
$.jMsg.manipulateGrid = function(param) {
    var index = param.index;
    delete param.index;

    $.extend($.jMsg.gridData[index], param);

    $.jMsg.generateGrid($.jMsg.gridData[index], index);
};

$.jMsg.newMsg = function(param) {
    var index = $.jMsg.gridData.length;

    $.jMsg.gridData[index] = $.extend({
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
        loading : '<div class="jMsg-ajax-loading"><div><span></span><label>Loading</label></div></div>',

        /* Execute if rendiring is complete */
        complete : function(){},

        /* data that will be included by submitting form */
        includeData : {}

    }, param);

    $(param.target).append($.jMsg.gridData[index].loading);

    $.jMsg.generateGrid($.jMsg.gridData[index], index);
};

$.jMsg.generateGrid = function(opt, index) {
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
                $(opt.target +' div[data-jMsg]').append(opt.loading);
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
        e = get_list();

        b = opt.topBar == true || opt.bottomBar == true ? barControl() : '';

        return opt.opening
                + '<div class="row" data-jMsg>'
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

        c  = '<div id="jMsg-action-bar"><span>';

        /* First button */
        if(cSegment-1 > numSegment && opt.hideFirstButton == false) {
            c += "<button class=\"jMsg-btn\" title=\"Next\" ";
            c += "onclick=\"$.jMsg.manipulateGrid({index:'"+index+"',offset:'0'})\">";
            c += opt.firstLabel +"</button>";
        }

        /* Previous button */
        if((offs = parseFloat(offset) - parseFloat(limit)) >= 0) {
            c += "<button class=\"jMsg-btn\" title=\"Previous\" ";
            c += "onclick=\"$.jMsg.manipulateGrid({index:'"+index+"',offset:'"+offs+"'})\">";
            c += opt.previousLabel +"</button>";
        }

        if(opt.hideSegments == false) {
            /* PAGINATION SEGMENTS */
            do {
                // if(((cSegment - 1) + counter) * limit <= dData.total)
                if(((cSegment - 1) + counter) * limit < dData.total)
                {
                    p += "<button onclick=\"$.jMsg.manipulateGrid({index:'"+index+"',";
                    p += "offset:"+(((cSegment - 1) + counter) * limit)+"})\">"+(cSegment + counter)+'</button>';
                }

                if(((cSegment - 1) - counter) * limit >= 0)
                {
                    p = "offset:"+(((cSegment - 1) - counter) * limit)+"})\">"+(cSegment - counter)+'</button>' + p;
                    p = "<button onclick=\"$.jMsg.manipulateGrid({index:'"+index+"'," + p;
                }

                counter++;
            }
            while(counter <= numSegment);

            c += p;
        }

        /* Next button */
        if((offs = parseFloat(offset) + parseFloat(limit)) < dData.total) {
            c += "<button class=\"jMsg-btn\" title=\"Next\" ";
            c += "onclick=\"$.jMsg.manipulateGrid({index:'"+index+"',";
            c += "offset:'"+offs+"'})\">";
            c += opt.nextLabel +"</button>";
        }

        /* LAST button */
        var lastOffset = dData.total % limit;
        if(Math.ceil(dData.total / limit) - (cSegment) > numSegment && opt.hideLastButton == false) {
            c += "<button class=\"jMsg-btn\" title=\"Next\" ";
            c += "onclick=\"$.jMsg.manipulateGrid({index:'"+index+"',";
            c += "offset:'"+(dData.total - (lastOffset == 0 ? limit : lastOffset))+"'})\">";
            c += opt.lastLabel +"</button>";
        }

        c += '</span></div>';

        return c;
    }

    function get_list(v) {
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
            var id = $.makeArray(obj['id']);
            var sender = $.makeArray(obj['sender']);
            var subject = $.makeArray(obj['subject']);
            var msg = $.makeArray(obj['message']);
            var isopenmsg = $.makeArray(obj['isopen']);

            var message = $.makeArray(obj['message_user']);

            if(message == 'lawdger_connect'){
                var user = 'Lawlists';
            } else {
                var user = 'Client';
            }

            var datesend = $.makeArray(obj['datesend']);
            if(isopenmsg == "1") {
                var isopen = "is_open";
            } else {
                var isopen = "not_open";
            }

            td += '<tr class="'+isopen+'">';
                td += '<td class="mailbox-name">';
                    td += sender;
                td += '</td>';
                td += '<td class="mailbox-subject">';
                    td += '<a href="view-queries/'+id+'"> <b>'+subject+'</b> - '+ msg +'...</a>';
                td += '</td>';
                td += '<td class="mailbox-attachment">';
                    td += '<i class="fa fa-paperclip"></i>';
                td += '</td>';
                td += '<td class="mailbox-date">';
                    td += datesend;
                td += '</td>';
                td += '<td >';
                    td += user;
                td += '</td>';
            td += '</tr>';
        });

        return  td ;
    }
};

$(function() {
    $('*[data-jMsgTarget]').on('click', function(e){
        e.preventDefault();
        var index;

        for(var i = 0; i < $.jMsg.gridData.length, typeof index == 'undefined'; i++) {
            if($.jMsg.gridData[i].target == $(this).attr('data-jMsgTarget')) {
                index = i;
                break;
            }
        }

        $.extend($.jMsg.gridData[index], {
            offset : 0,
        });

        $.jMsg.generateGrid($.jMsg.gridData[index], index);
    });
});