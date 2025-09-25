$.jGrid = {};
$.jGrid.gridData = [];
$.jGrid.manipulateGrid = function(param) {
    var index = param.index;
    delete param.index;

    $.extend($.jGrid.gridData[index], param);

    $.jGrid.generateGrid($.jGrid.gridData[index], index);
};

$.jGrid.newGrid = function(param) {
    var index = $.jGrid.gridData.length;

    $.jGrid.gridData[index] = $.extend({
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
        previousLabel : 'Prev',

        /* Label of Next button */
        nextLabel : 'Next',

        /* Label of First button */
        firstLabel : 'First',

        /* Label of Last button */
        lastLabel : 'Last',

        /* Loading image */
        loading : '<div class="jgrid-ajax-loading"><div><span></span><label>Loading</label></div></div>',

        /* Execute if rendiring is complete */
        complete : function(){},

        /* data that will be included by submitting form */
        includeData : {}

    }, param);

    $(param.target).append($.jGrid.gridData[index].loading);

    $.jGrid.generateGrid($.jGrid.gridData[index], index);
};

$.jGrid.generateGrid = function(opt, index) {
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
                $(opt.target +' div[data-jgrid]').append(opt.loading);
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
        e = '<div><table class="table table-hover">';
            e += '<thead>';
                e += gen_th();
            e += '</thead>';
            e += '<tbody>';
                e += get_td();
            e += '</tbody>';
        e += '</table></div>';

        b = opt.topBar == true || opt.bottomBar == true ? barControl() : '';

        return opt.opening
                + '<div data-jgrid>'
                + (opt.topBar == true ? b : '')
                + e
                + (opt.bottomBar == true ? b : '')
                + '</div>'
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

        c  = '<div id="jgrid-action-bar"><span>';

        /* First button */
        if(cSegment-1 > numSegment && opt.hideFirstButton == false) {
            c += "<button class=\"jgrid-btn\" title=\"Next\" ";
            c += "onclick=\"$.jGrid.manipulateGrid({index:'"+index+"',offset:'0'})\">";
            c += opt.firstLabel +"</button>";
        }

        /* Previous button */
        if((offs = parseFloat(offset) - parseFloat(limit)) >= 0) {
            c += "<button class=\"jgrid-btn\" title=\"Previous\" ";
            c += "onclick=\"$.jGrid.manipulateGrid({index:'"+index+"',offset:'"+offs+"'})\">";
            c += opt.previousLabel +"</button>";
        }

        if(opt.hideSegments == false) {
            /* PAGINATION SEGMENTS */
            do {
                // if(((cSegment - 1) + counter) * limit <= dData.total)
                if(((cSegment - 1) + counter) * limit < dData.total)
                {
                    p += "<button onclick=\"$.jGrid.manipulateGrid({index:'"+index+"',";
                    p += "offset:"+(((cSegment - 1) + counter) * limit)+"})\">"+(cSegment + counter)+'</button>';
                }

                if(((cSegment - 1) - counter) * limit >= 0)
                {
                    p = "offset:"+(((cSegment - 1) - counter) * limit)+"})\">"+(cSegment - counter)+'</button>' + p;
                    p = "<button onclick=\"$.jGrid.manipulateGrid({index:'"+index+"'," + p;
                }

                counter++;
            }
            while(counter <= numSegment);

            c += p;
        }

        /* Next button */
        if((offs = parseFloat(offset) + parseFloat(limit)) < dData.total) {
            c += "<button class=\"jgrid-btn\" title=\"Next\" ";
            c += "onclick=\"$.jGrid.manipulateGrid({index:'"+index+"',";
            c += "offset:'"+offs+"'})\">";
            c += opt.nextLabel +"</button>";
        }

        /* LAST button */
        var lastOffset = dData.total % limit;
        if(Math.ceil(dData.total / limit) - (cSegment) > numSegment && opt.hideLastButton == false) {
            c += "<button class=\"jgrid-btn\" title=\"Next\" ";
            c += "onclick=\"$.jGrid.manipulateGrid({index:'"+index+"',";
            c += "offset:'"+(dData.total - (lastOffset == 0 ? limit : lastOffset))+"'})\">";
            c += opt.lastLabel +"</button>";
        }

        c += '</span></div>';

        return c;
    }

    function get_td() {
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

        $.each(dData.data, function(indexData, data) {
            td += '<tr style="height:70px">';

            if(opt.count == true) {
                count++;
                td += '<td><strong>'+ (count) +'</strong></td>';
            }

            $.each(opt.columns, function(indexColumns, columns) {
                $.each(data, function(indexData2, data2) {
                    if(indexColumns == indexData2) {
                        td += '<td valign="center">'+ data2 +'</td>';
                        return false;
                    }
                });
            });

            td += '</tr>';
        });

        return td;
    }

    function gen_th(v) {
        var th = '<tr>';

        if(opt.count == true) {
            th += '<th><strong>#</strong></th>';
        }

        $.each(opt.columns, function(k, v) {
            th += '<th';
            th += typeof v.style != "undefined" && v.style != ''? ' style="' + v.style + '"' : '';
            th += typeof v.attr  != "undefined"? ' ' + v.attr : '';
            th += '>';

            var sortable = typeof v.sortable != 'undefined' && v.sortable == true;

            if(sortable == true) {
                var o = "index:'"+index+"',sort:'"+ k.trim() +"',order:'"+(opt.sort == k && opt.order == 'asc' ? 'desc' : 'asc')+"'";
                th += '<a style="color:#fff" href="javascript:void(0)" onclick="$.jGrid.manipulateGrid({'+o+'})">';
            }

            th += v.name;

            if(sortable == true) {
                th += '<i class="jgrid-icon jgrid-icon-';
                th += opt.sort == k ? ( opt.order == 'asc' ? 'asc' : 'desc' ) : 'unsorted';
                th += '"></i>';
                th += '</a>';
            }

            th += '</th>';
        });

        return th + '</tr>';
    }
};

$(function() {
    $('*[data-jGridTarget]').on('click', function(e){
        e.preventDefault();
        var index;

        for(var i = 0; i < $.jGrid.gridData.length, typeof index == 'undefined'; i++) {
            if($.jGrid.gridData[i].target == $(this).attr('data-jGridTarget')) {
                index = i;
                break;
            }
        }

        $.extend($.jGrid.gridData[index], {
            offset : 0,
        });

        $.jGrid.generateGrid($.jGrid.gridData[index], index);
    });
});