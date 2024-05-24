const biReportContainer = document.getElementById('bi-reports');

const reports = [
    {
        'name': 'Port Statistics',
    },
    {
        'name': 'Line Up - Forecast'
    }
]
{/* <li><a href="pbi_report.php">Port Statistics</a></li> */}

reports.forEach(report => {
    const reportList = document.createElement('li');
    const reportLink = document.createElement('a');
    reportLink.href = `pbi_report.php?reportName=${report.name}`;
    reportLink.innerText = report.name;
    reportList.appendChild(reportLink);
    // reportList.addEventListener('click', () => {
    //     loadReport(report.id);
    // });
    biReportContainer.appendChild(reportList);
});