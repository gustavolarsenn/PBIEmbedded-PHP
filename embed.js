let models = window["powerbi-client"].models;
let reportContainer = $("#report-container")[0];

$(document).ready(function() {
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const reportName = urlParams.get('reportName');
        const reportTitle = document.getElementById('report-title');
        reportTitle.innerText = reportName;
        loadReport("pbi_report.php?reportName=" + encodeURIComponent(reportName));
    });
});

function loadReport(reportLinkFix) {
    // Create a config object with type of the object, Embed details and Token Type
    const reportContainer_ = document.querySelector('#report-container')
    const loaderContainer_ = document.querySelector('#preloader-report')
    reportContainer_.style.display = "none";
    loaderContainer_.style.display = "flex";
    // AJAX request to get Embed token
    $.ajax({
        type: "GET",
        url: reportLinkFix + "&json=true",
        // url: "/pbi_auth.php",
        dataType: "json",
        success: function(embedData) {

            let reportLoadConfig = {
                type: "report",
                tokenType: models.TokenType.Embed,
                accessToken: embedData.embedToken,

                // Use other embed report config based on the requirement. We have used the first one for demo purpose
                embedUrl: embedData.reportsDetail[0].embedUrl,

                // Enable this setting to remove gray shoulders from embedded report
                settings: {
                    navContentPaneEnabled: false,
                    panes: {
                        filters: {
                            visible: false
                        },
                    },
                }
            };

            // Use the token expiry to regenerate Embed token for seamless end user experience
            // Refer https://aka.ms/RefreshEmbedToken
            tokenExpiry = embedData.expiry;

            // Embed Power BI report when Access token and Embed URL are available
            let report = powerbi.embed(reportContainer, reportLoadConfig);

            const fullscreenButton = document.getElementById('fullscreen');
            fullscreenButton.addEventListener('click', () => {
                report.fullscreen();
            });

            const downloadButton = document.getElementById('download');
            downloadButton.addEventListener('click', () => {
                report.print();
            });

            // Clear any other loaded handler events
            report.off("loaded");

            // Triggers when a report schema is successfully loaded
            report.on("loaded", function() {
                console.log("Report load successful");
                reportContainer_.style.display = "flex";
                loaderContainer_.style.display = "none";
            });

            // Clear any other rendered handler events
            report.off("rendered");

            // Triggers when a report is successfully embedded in UI
            report.on("rendered", function() {
                console.log("Report render successful");
            });

            // Clear any other error handler events
            report.off("error");
            
            // Handle embed errors
            report.on("error", function(event) {
                let errorMsg = event.detail;
                return;
            });
        },

        error: function(err) {
            // Show error container
            $(".embed-container").hide();
            $(".embed-section").hide();
            let errorContainer = $(".error-container");

            errorContainer.show();
            // Get the error message from err object
            let errMsg = JSON.parse(err.responseText)["error"];

            // Split the message with \r\n delimiter to get the errors from the error message
            let errorLines = errMsg.split("\r\n");

            // Create error header
            let errHeader = document.createElement("p");
            let strong = document.createElement("strong");
            let node = document.createTextNode(
                "Erro ao carregar relatório. Recarregue a página ou contate o administrador do sistema. Detalhes do erro:\n"
            );

            // Get the error container
            let errContainer = errorContainer.get(0);

            // Add the error header in the container
            strong.appendChild(node);
            errHeader.appendChild(strong);
            errContainer.appendChild(errHeader);

            // Create <p> as per the length of the array and append them to the container
            errorLines.forEach((element) => {
                let errorContent = document.createElement("p");
                let node = document.createTextNode(element);
                errorContent.appendChild(node);
                errContainer.appendChild(errorContent);
            });
        },
    });
}