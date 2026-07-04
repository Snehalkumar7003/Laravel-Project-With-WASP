let activityOffset = 0;
let activityLoading = false;
let activityFinished = false;

$(function(){
    loadActivities();

    $("#activityTimeline").on("scroll", function () {
        const container = $(this);
        if (
            container.scrollTop() +
            container.innerHeight() >=
            container[0].scrollHeight - 100
        ) {
            loadActivities();
        }
    });
});

function loadActivities() {
    if (activityLoading || activityFinished) {
        return;
    }
    activityLoading = true;
    $("#activityLoader").removeClass("d-none");
    $.ajax({
        url: APP_CONFIG.BASE_URL + "/app/profile/activity",
        type: "GET",
        data: {
            offset: activityOffset
        },
        success: function(response){
            appendActivities(response.data);
            activityOffset += response.data.length;
            activityFinished = !response.hasMore;
        },
        complete: function(){
            activityLoading = false;
            $("#activityLoader").addClass("d-none");
        }
    });
}

function appendActivities(logs){
    let html = "";
    logs.forEach(function(log,index){
        html += createActivity(log,index === logs.length - 1);
    });
    $("#activityTimeline").append(html);
    lucide.createIcons();
}

function createActivity(log,isLast){
    return `
        <div class="flex gap-3">
            <div class="relative flex flex-col items-center">
                <div class="w-8 h-8 rounded-lg bg-${log.color}-500 flex items-center justify-center shrink-0">
                    <i data-lucide="${log.icon}" class="lucide-xs text-white"></i>
                </div>
                ${!isLast ? '<div class="up-activity-line"></div>' : ''}
            </div>
            <div class="">
                <p class="text-sm text-slate-700">
                    ${escapeHtml(log.remarks)}
                </p>
                <p class="text-xs text-slate-400">
                    ${log.display_date}
                </p>
            </div>
        </div>
    `;
}

function escapeHtml(text){
    return $("<div>").text(text ?? "").html();
}