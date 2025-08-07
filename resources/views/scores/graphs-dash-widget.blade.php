<div class="card mb-5 mb-xxl-8">
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Progress Graphs</span>
        </h3>
        <div class="card-toolbar">
            <button class="btn btn-primary" id="viewallgraph"><i class="fa fa-solid fa-chart-line"></i> <span>Hide All Graphs</span>
            </button>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        var $viewGraph = $("#viewallgraph");
        $viewGraph.click(function () {
            var privatenotetext = $('#viewallgraph').text();
            if ($viewGraph.hasClass('show-all')) {
                $viewGraph.removeClass('show-all').find('span').text("Hide All Graphs");
            } else {
                $viewGraph.addClass('show-all').find('span').text("Show All Graphs");
            }

            $("#graph-display").slideToggle();
        });
    });
</script>
<div id="graph-display" style="">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">
                <div class="card-body">
                    Graphs not available
                </div>
            </div>
        </div>
    </div>
</div>
