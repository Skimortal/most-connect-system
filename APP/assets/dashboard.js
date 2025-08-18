import Chart from 'chart.js/auto';


var doughnutChart;
function initDoughnutChart() {
    if(typeof doughnutChart !== "undefined") {
        doughnutChart.destroy();
    }

    const canvas = document.getElementById('doughnut-chart');

    if(canvas) {
        var invoicesOpen = canvas.getAttribute('data-invoices-open')
        var invoicesOpenLabel = canvas.getAttribute('data-invoices-open-label')
        var invoicesSent = canvas.getAttribute('data-invoices-sent')
        var invoicesSentLabel = canvas.getAttribute('data-invoices-sent-label')
        var invoicesPayed = canvas.getAttribute('data-invoices-payed')
        var invoicesPayedLabel = canvas.getAttribute('data-invoices-payed-label')

        var data = {
            labels: [
                invoicesOpenLabel,
                invoicesSentLabel,
                invoicesPayedLabel
            ],
            datasets: [{
                label: '',
                data: [invoicesOpen, invoicesSent, invoicesPayed],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
            }]
        };

        new Chart(canvas, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {position: 'bottom'}
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', initDoughnutChart);
document.addEventListener('turbo:render', initDoughnutChart);
