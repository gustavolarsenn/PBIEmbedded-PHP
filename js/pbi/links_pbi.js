const biReportContainer = document.getElementById('bi-reports');

fetch('../../controllers/PbiReportsController.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
        action: 'getActiveReports',
    }),
})
.then(response => {
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}, statusText: ${response.statusText}`);
    }
    return response.json();
})
.then(data => {
    Object.keys(data).forEach(function(report) {
        const reportList = document.createElement('li');
        const reportLink = document.createElement('a');
        reportLink.classList.add('report-link');
        reportLink.href = `../PBI/relatorio_pbi.php?reportName=${report}`;
        reportLink.innerText = report;
        reportList.appendChild(reportLink);
        biReportContainer.appendChild(reportList);
    });
})
.catch(error => console.error('Error:', error));