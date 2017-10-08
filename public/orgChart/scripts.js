'use strict';

(function($){

  $(function() {

    $('#chart-container').orgchart({
      'data' : datascource,
      // 'depth': 2,
      'pan': true,
      'zoom': true,
      'toggleSiblingsResp':true,
      'exportButton':true,
      
      'nodeTitle': 'fullname',
      'nodeContent': 'designation_name',
      'nodeID': 'id',
      'createNode': function($node, data) {
        var secondMenuIcon = $('<i>', {
          'class': 'fa fa-info-circle second-menu-icon',
          click: function() {
            $(this).siblings('.second-menu').toggle();
          }
        });
        var secondMenu = '<div class="second-menu"><img class="avatar" src="/files/' + config_id + '/' +data.id+ '/' + data.image+'"></div>';
        // var secondMenu = '<div class="second-menu"><img class="avatar" src="'+ data.image+'"></div>';
        $node.append(secondMenuIcon).append(secondMenu);
      }
    });

  });

})(jQuery);