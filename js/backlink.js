    if ($.fn.DataTable.isDataTable("#backlink-list")) {
    $.fn.dataTable.ext.errMode = 'none'
    }

    if ($.fn.DataTable.isDataTable("#domain-list")) {
    $.fn.dataTable.ext.errMode = 'none'
    }

    $("#backlink-list").DataTable({
        "aaSorting": [],
        "pageLength": 100,
        "searching": false,
        "lengthChange": false,
        "pagingType": "full_numbers",
        "info": false,
        "scrollX": true,
        "bJQueryUI": true,
        "bAutoWidth": false, // Disable the auto width calculation 
        "oLanguage": {
            "oPaginate": {
                "sNext": '<span class="pagination-fa"><i class="fa fa-angle-right" ></i></span>',
                "sFirst": '<span class="pagination-fa"><i class="fa fa-angle-double-left " ></i></span>',
                "sLast": '<span class="pagination-fa"><i class="fa fa-angle-double-right " ></i></span>',
                "sPrevious": '<span class="pagination-fa"><i class="fa fa-angle-left" ></i></span>'
            }
        }     
        
    });

    $("#domain-list").DataTable({
        "aaSorting": [],
        "pageLength": 100,
        "searching": false,
        "lengthChange": false,
        "info": false,
        "scrollX": true,
        "pagingType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false, // Disable the auto width calculation 
        "aoColumns": [
          { "sWidth": "45%" }, // 1st column width 
          { "sWidth": "45%" }, // 2nd column width 
          { "sWidth": "10%" } // 3rd column width and so on 
        ],
        "oLanguage": {
            "oPaginate": {
                "sNext": '<span class="pagination-fa"><i class="fa fa-angle-right" ></i></span>',
                "sFirst": '<span class="pagination-fa"><i class="fa fa-angle-double-left " ></i></span>',
                "sLast": '<span class="pagination-fa"><i class="fa fa-angle-double-right " ></i></span>',
                "sPrevious": '<span class="pagination-fa"><i class="fa fa-angle-left" ></i></span>'
            }
        }      
        
    });
       
              