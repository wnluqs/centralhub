(function ($) {
    "use strict";

    var fullHeight = function () {
        $(".js-fullheight").css("height", $(window).height());
        $(window).resize(function () {
            $(".js-fullheight").css("height", $(window).height());
        });
    };
    fullHeight();

    $("#sidebarCollapse").on("click", function () {
        $("#sidebar").toggleClass("active");
    });
})(jQuery);

(function ($) {
    "use strict";

    // Check if the canvas element exists
    let chartCanvas = document.getElementById("parkingChart");
    if (chartCanvas) {
        let ctx = chartCanvas.getContext("2d");
        // Initialize the chart and assign it to a global variable
        window.parkingChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: [], // Time slots will go here
                datasets: [
                    {
                        label: "Paid",
                        data: [],
                        borderColor: "green",
                        backgroundColor: "rgba(0, 255, 0, 0.1)",
                        fill: true,
                    },
                    {
                        label: "Unpaid",
                        data: [],
                        borderColor: "red",
                        backgroundColor: "rgba(255, 0, 0, 0.1)",
                        fill: true,
                    },
                    {
                        label: "Pending Payment",
                        data: [],
                        borderColor: "yellow",
                        backgroundColor: "rgba(255, 255, 0, 0.1)",
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Time Slot",
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Count",
                        },
                    },
                },
            },
        });
    }

    // Now fetch the data and update the chart
    fetch("/api/parking-data")
        .then((response) => response.json())
        .then((data) => {
            let labels = [];
            let paidData = [];
            let unpaidData = [];
            let pendingData = [];

            data.forEach((row) => {
                labels.push(row.minute_slot);
                paidData.push(row.paid_count);
                unpaidData.push(row.unpaid_count);
                pendingData.push(row.pending_count);
            });

            if (window.parkingChart) {
                window.parkingChart.data.labels = labels;
                window.parkingChart.data.datasets[0].data = paidData; // Paid
                window.parkingChart.data.datasets[1].data = unpaidData; // Unpaid
                window.parkingChart.data.datasets[2].data = pendingData; // Pending
                window.parkingChart.update();
            }
        })
        .catch((error) => console.error(error));
})(jQuery);
