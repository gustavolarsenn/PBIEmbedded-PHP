// Default export is a4 paper, portrait, using milimeters for units

// function gerarPDF(){
//   var css = '@page { size: landscape; }',
//     head = document.head || document.getElementsByTagName('head')[0],
//     style = document.createElement('style');

//     style.type = 'text/css';
//     style.media = 'print';

//     if (style.styleSheet){
//       style.styleSheet.cssText = css;
//     } else {
//       style.appendChild(document.createTextNode(css));
//     }

//     head.appendChild(style);

//     window.print();
// }

function gerarPDF() {
    // var doc = new jsPDF()
  // var pdf = new jsPDF('p', 'pt', 'a4');

    let reportPageWidth = $('#relatorio-container').innerWidth();
    let reportPageHeight = $('#relatorio-container').innerHeight();

  // create a new canvas object that we will populate with all other canvas objects
  var pdfCanvas = $('<canvas />').attr({
    id: "canvaspdf",
    width: reportPageWidth,
    height: reportPageHeight
  });
  
  // keep track canvas position
  var pdfctx = $(pdfCanvas)[0].getContext('2d');
  var pdfctxX = 0;
  var pdfctxY = 0;
  var buffer = 100;
  
  // $("canvas").each(function(index) {
  $(".render-pdf").each(function(index) {
    // get the chart height/width
    var canvasHeight = $(this).innerHeight();
    var canvasWidth = $(this).innerWidth();

    if (canvasHeight === 0 || canvasWidth === 0) {
      return;
    }

    // draw the chart into the new canvas
    // pdfctx.drawImage($(this)[0], pdfctxX, pdfctxY, canvasWidth, canvasHeight);
    pdfctx.addHTML($(this)[0], pdfctxX, pdfctxY, { allowTaint: true, useCORS: true, pagesplit: false }, function () {
    });

    // pdfctx.drawImage($(this)[0], pdfctxX, pdfctxY, canvasWidth, canvasHeight);
    pdfctxX += canvasWidth + buffer;
    
    // our report page is in a grid pattern so replicate that in the new canvas
    if (index % 2 === 1) {
      pdfctxX = 0;
      pdfctxY += canvasHeight + buffer;
    }
  });
  
  // create new pdf and add our new canvas as an image
  var pdf = new jsPDF('l', 'pt', [reportPageWidth, reportPageHeight]);
  pdf.addImage($(pdfCanvas)[0], 'PNG', 0, 0);
  
  // download the pdf
  pdf.save('filename.pdf');
};

// // function gerarPDF() {
// //   var pdf = new jsPDF('p', 'pt', 'a4');

// //   let content = document.getElementById('relatorio-container');
// //   content.style.backgroundColor = 'white';
// //   pdf.addHTML(content, 0, -20, { allowTaint: true, useCORS: true, pagesplit: false }, function () {
// //   pdf.save('teste.pdf');
// // });
// // }

export { gerarPDF };