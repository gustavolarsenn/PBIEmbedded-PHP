let models = window["powerbi-client"].models;
let reportContainer = $("#report-container").get(0);

$(document).ready(function() {
    $(document).ready(async function() {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const reportName = urlParams.get('reportName');

            const response = await fetch('/controllers/RelatorioPBIController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'buscarInformacoesRelatorio',
                    reportName: reportName
                }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}, statusText: ${response.statusText}`);
            }

            const data = await response.json();

            const reportTitle = document.getElementById('report-title');
            reportTitle.innerText = data.relatorio;
            loadReport("/controllers/RelatorioPBIController.php?reportName=" + encodeURIComponent(reportName), reportName);

        } catch (error) {
            tratarErro(error);
        }
    });
});

function tratarErro(erro = "Erro ao carregar relatório. Recarregue a página ou contate o administrador do sistema."){
    // Show error container
    $(".embed-container").hide();
    $(".embed-section").hide();
    let errorContainer = $(".error-container");

    errorContainer.show();
    // Split the message with \r\n delimiter to get the errors from the error message
    try {
        let errorLines = erro.split("\r\n");
        
        // "Erro ao puxar dados em API da Azure e carregar relatório. Recarregue a página ou contate o administrador do sistema. Detalhes do erro:\n"
        // Create error header
        let errHeader = document.createElement("p");
        let strong = document.createElement("strong");
        let node = document.createTextNode(
            "Erro"
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
    } catch (error) {
        let errorContent = document.createElement("p");
        let node = document.createTextNode(erro);
        errorContent.appendChild(node);
        errorContainer.get(0).appendChild(errorContent);
    }
    const reportContainer_ = document.querySelector('#report-container')
    const loaderContainer_= document.querySelector('#preloader-report')
    reportContainer_.style.display = "flex";
    loaderContainer_.style.display = "none";
    return;
}

function loadReport(reportLinkFix, report) {
    $.ajax({
        type: "POST",
        url: reportLinkFix + "&json=true",
        data: {
            action: "gerarRelatorioPBI",
            reportName: report
        },
        dataType: "json",
        success: function(embedData) {
            const loaderMessage_= document.querySelector('#loader-message')
            loaderMessage_.innerText = "Carregando relatório..."

            if (embedData.sucesso === false) {
                tratarErro(embedData.mensagem);
                return;
            }
            
            const reportContainer_ = document.querySelector('#report-container')
            const loaderContainer_= document.querySelector('#preloader-report')

            embedInfo = JSON.parse(embedData.dados);

            let reportLoadConfig = {
                type: "report",
                tokenType: models.TokenType.Embed,
                accessToken: embedInfo.embedToken,

                // Use other embed report config based on the requirement. We have used the first one for demo purpose
                embedUrl: embedInfo.reportsDetail[0].embedUrl,

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
            tokenExpiry = embedInfo.expiry;

            // Embed Power BI report when Access token and Embed URL are available
            let report = powerbi.embed(reportContainer, reportLoadConfig);

            const fullscreenButton = document.getElementById('fullscreen');
            fullscreenButton.addEventListener('click', () => {
                report.fullscreen();
            });

            const downloadButton = document.getElementById('download');
            downloadButton.addEventListener('click', () => {
                // report.print();

                report.exportToFile('PDF').then(function(pdfData) {
                    const blob = new Blob([pdfData], { type: 'application/pdf' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'report.pdf';
                    a.click();
                })
            });

            // Clear any other loaded handler events
            report.off("loaded");
            
            reportContainer_.style.display = "flex";
            loaderContainer_.style.display = "none";

            // Triggers when a report schema is successfully loaded
            report.on("loaded", function() {
                console.log("Report load successful");
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

        error: tratarErro
    });
}