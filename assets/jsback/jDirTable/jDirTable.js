$.jDirTable = {};
$.jDirTable.gridData = [];
$.jDirTable.manipulateGrid = function(param) {
    var index = param.index;
    delete param.index;

    $.extend($.jDirTable.gridData[index], param);

    $.jDirTable.generateGrid($.jDirTable.gridData[index], index);
};

$.jDirTable.newDirGrid = function(param) {
    var index = $.jDirTable.gridData.length;

    $.jDirTable.gridData[index] = $.extend({
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
        loading : '<div class="jDirTable-ajax-loading"><div><span></span><label>Loading</label></div></div>',

        /* Execute if rendiring is complete */
        complete : function(){},

        /* data that will be included by submitting form */
        includeData : {}

    }, param);

    $(param.target).append($.jDirTable.gridData[index].loading);

    $.jDirTable.generateGrid($.jDirTable.gridData[index], index);
};

$.jDirTable.generateGrid = function(opt, index) {
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
                $(opt.target +' div[data-jDirTable]').append(opt.loading);
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
                + '<div class="row" data-jDirTable>'
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

        c  = '<div id="jDirTable-action-bar"><span>';

        /* First button */
        if(cSegment-1 > numSegment && opt.hideFirstButton == false) {
            c += "<button class=\"jDirTable-btn\" title=\"Next\" ";
            c += "onclick=\"$.jDirTable.manipulateGrid({index:'"+index+"',offset:'0'})\">";
            c += opt.firstLabel +"</button>";
        }

        /* Previous button */
        if((offs = parseFloat(offset) - parseFloat(limit)) >= 0) {
            c += "<button class=\"jDirTable-btn\" title=\"Previous\" ";
            c += "onclick=\"$.jDirTable.manipulateGrid({index:'"+index+"',offset:'"+offs+"'})\">";
            c += opt.previousLabel +"</button>";
        }

        if(opt.hideSegments == false) {
            /* PAGINATION SEGMENTS */
            do {
                // if(((cSegment - 1) + counter) * limit <= dData.total)
                if(((cSegment - 1) + counter) * limit < dData.total)
                {
                    p += "<button onclick=\"$.jDirTable.manipulateGrid({index:'"+index+"',";
                    p += "offset:"+(((cSegment - 1) + counter) * limit)+"})\">"+(cSegment + counter)+'</button>';
                }

                if(((cSegment - 1) - counter) * limit >= 0)
                {
                    p = "offset:"+(((cSegment - 1) - counter) * limit)+"})\">"+(cSegment - counter)+'</button>' + p;
                    p = "<button onclick=\"$.jDirTable.manipulateGrid({index:'"+index+"'," + p;
                }

                counter++;
            }
            while(counter <= numSegment);

            c += p;
        }

        /* Next button */
        if((offs = parseFloat(offset) + parseFloat(limit)) < dData.total) {
            c += "<button class=\"jDirTable-btn\" title=\"Next\" ";
            c += "onclick=\"$.jDirTable.manipulateGrid({index:'"+index+"',";
            c += "offset:'"+offs+"'})\">";
            c += opt.nextLabel +"</button>";
        }

        /* LAST button */
        var lastOffset = dData.total % limit;
        if(Math.ceil(dData.total / limit) - (cSegment) > numSegment && opt.hideLastButton == false) {
            c += "<button class=\"jDirTable-btn\" title=\"Next\" ";
            c += "onclick=\"$.jDirTable.manipulateGrid({index:'"+index+"',";
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
            console.log(indexData); 
            var obj = data;
            var arr = $.makeArray(obj);
            
            var title = $.makeArray(obj['name']);
            var contact = $.makeArray(obj['contact']);
            var address = $.makeArray(obj['address']);
            var location = $.makeArray(obj['location']);


            td += '<div class="container mb30">';

                td += '<h3 class="fw-400 text-primary mb10">'+location+'</h3>';   
                td += '<div class="row">';
                    td += '<div class="col-md-5">';
                        td += '<p class="fw-400">'+title+'</p>';   
                    td += '</div>';
                    td += '<div class="col-md-4">';
                        td += '<p>'+address+'</p>';   
                    td += '</div>';
                    td += '<div class="col-md-3">';
                        td += '<p>'+contact+'</p>';   
                    td += '</div>';
                td += '</div>';
            td += '</div>';
        });

        return  td ;
    }
};

$(function() {
    $('*[data-jDirTableTarget]').on('click', function(e){
        e.preventDefault();
        var index;

        for(var i = 0; i < $.jDirTable.gridData.length, typeof index == 'undefined'; i++) {
            if($.jDirTable.gridData[i].target == $(this).attr('data-jDirTableTarget')) {
                index = i;
                break;
            }
        }

        $.extend($.jDirTable.gridData[index], {
            offset : 0,
        });

        $.jDirTable.generateGrid($.jDirTable.gridData[index], index);
    });
});