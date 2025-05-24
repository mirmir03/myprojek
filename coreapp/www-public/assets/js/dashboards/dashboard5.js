// -------------------------------------------------------------------------------------------------------------------------------------------
// Dashboard 5 : Chart Init Js
// -------------------------------------------------------------------------------------------------------------------------------------------
$(function () {
    "use strict";
    // -----------------------------------------------------------------------
    // Total revenue chart
    // -----------------------------------------------------------------------

    var total_revenue = {
        series: [
            { name: "Inbound Calls", data: [65, 80, 80, 60, 60, 45, 45, 80, 80, 70, 70, 90, 90, 80, 80, 80, 60, 60, 50] },
            { name: "Outbound Calls", data: [90, 110, 110, 95, 95, 85, 85, 95, 95, 115, 115, 100, 100, 115, 115, 95, 95, 85, 85] },
        ],
        chart: { fontFamily: "inherit", type: "area", height: 240, toolbar: { show: !1 } },
        plotOptions: {},
        legend: { show: !1 },
        dataLabels: { enabled: !1 },
        fill: { type: "solid", opacity: 0.07, colors: ["#04B440", "#009EFB"] },
        stroke: { curve: "smooth", show: !0, width: 2, colors: ["#04B440", "var(--bs-info)"] },
        xaxis: {
            categories: ["", "8 AM", "81 AM", "9 AM", "10 AM", "11 AM", "12 PM", "13 PM", "14 PM", "15 PM", "16 PM", "17 PM", "18 PM", "18:20 PM", "18:20 PM", "19 PM", "20 PM", "21 PM", ""],
            axisBorder: { show: !1 },
            axisTicks: { show: !1 },
            tickAmount: 6,
            labels: { rotate: 0, rotateAlways: !0, style: { fontSize: "12px", colors: "#a1aab2" } },
            crosshairs: { position: "front", stroke: { color: ["var(--bs-success)", "var(--bs-info)"], width: 1, dashArray: 3 } },

        },
        yaxis: { max: 120, min: 30, tickAmount: 6, labels: { style: { fontSize: "12px", colors: "#a1aab2" } } },
        states: { normal: { filter: { type: "none", value: 0 } }, hover: { filter: { type: "none", value: 0 } }, active: { allowMultipleDataPointsSelection: !1, filter: { type: "none", value: 0 } } },
        tooltip: {
            theme: "dark",
        },
        colors: ["var(--bs-success)", "var(--bs-info)"],
        grid: { borderColor: "var(--bs-border-color)", strokeDashArray: 4, yaxis: { lines: { show: !0 } } },
        markers: { strokeColor: ["var(--bs-success)", "var(--bs-info)"], strokeWidth: 3 },
    };

    var chart_area_spline = new ApexCharts(
        document.querySelector("#total-revenue"),
        total_revenue
    );
    chart_area_spline.render();


    // -----------------------------------------------------------------------
    // New Clients
    // -----------------------------------------------------------------------


    var options = {
        color: "#adb5bd",
        series: [12, 88],
        labels: ["12", "88"],
        chart: {
            type: "donut",
            height: 95,
            fontFamily: "inherit",
            foreColor: "#adb0bb",
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '85%',
                    background: 'transparent',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            offsetY: 7,
                        },
                        value: {
                            show: false,
                        },
                        total: {
                            show: true,
                            color: 'var(--bs-primary)',
                            fontSize: '14px',
                            fontWeight: "500",
                            label: '86',
                        },
                    },
                },
            },
        },
        grid: {
            show: false,
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        },
        stroke: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },

        legend: {
            show: false,
        },
        colors: ["var(--bs-primary)", "#9fa0a5",],

        tooltip: {
            theme: "dark",
            fillSeriesColor: false,
        },
    };

    var chart = new ApexCharts(document.querySelector("#new-clients"), options);
    chart.render();



    // -----------------------------------------------------------------------
    // All Projects
    // -----------------------------------------------------------------------


    var options = {
        color: "#adb5bd",
        series: [20, 80],
        labels: ["20", "80"],
        chart: {
            type: "donut",
            height: 95,
            fontFamily: "inherit",
            foreColor: "#adb0bb",
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '85%',
                    background: 'transparent',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            offsetY: 7,
                        },
                        value: {
                            show: false,
                        },
                        total: {
                            show: true,
                            color: 'var(--bs-danger)',
                            fontSize: '14px',
                            fontWeight: "500",
                            label: '248',
                        },
                    },
                },
            },
        },
        grid: {
            show: false,
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        },
        stroke: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },

        legend: {
            show: false,
        },
        colors: ["var(--bs-danger)", "#9fa0a5",],

        tooltip: {
            theme: "dark",
            fillSeriesColor: false,
        },
    };

    var chart = new ApexCharts(document.querySelector("#all-projects"), options);
    chart.render();




    // -----------------------------------------------------------------------
    // New Items
    // -----------------------------------------------------------------------


    var options = {
        color: "#adb5bd",
        series: [30, 70],
        labels: ["30", "70"],
        chart: {
            type: "donut",
            height: 95,
            fontFamily: "inherit",
            foreColor: "#adb0bb",
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '85%',
                    background: 'transparent',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            offsetY: 7,
                        },
                        value: {
                            show: false,
                        },
                        total: {
                            show: true,
                            color: 'var(--bs-warning)',
                            fontSize: '14px',
                            fontWeight: "500",
                            label: '352',
                        },
                    },
                },
            },
        },
        grid: {
            show: false,
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        },
        stroke: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },

        legend: {
            show: false,
        },
        colors: ["var(--bs-warning)", "#9fa0a5",],

        tooltip: {
            theme: "dark",
            fillSeriesColor: false,
        },
    };

    var chart = new ApexCharts(document.querySelector("#new-items"), options);
    chart.render();



    // -----------------------------------------------------------------------
    // Invoices
    // -----------------------------------------------------------------------


    var options = {
        color: "#adb5bd",
        series: [60, 40],
        labels: ["60", "40"],
        chart: {
            type: "donut",
            height: 95,
            fontFamily: "inherit",
            foreColor: "#adb0bb",
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '85%',
                    background: 'transparent',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            offsetY: 7,
                        },
                        value: {
                            show: false,
                        },
                        total: {
                            show: true,
                            color: 'var(--bs-success)',
                            fontSize: '14px',
                            fontWeight: "500",
                            label: '159',
                        },
                    },
                },
            },
        },
        grid: {
            show: false,
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        },
        stroke: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },

        legend: {
            show: false,
        },
        colors: ["var(--bs-success)", "#9fa0a5",],

        tooltip: {
            theme: "dark",
            fillSeriesColor: false,
        },
    };

    var chart = new ApexCharts(document.querySelector("#invoices"), options);
    chart.render();




    // -----------------------------------------------------------------------
    // doughnut chart option
    // -----------------------------------------------------------------------

    var option_Sales_of_the_Month = {
        series: [9, 3, 2, 2],
        labels: ["Social", "Marketing", "Search Engine", "Organic Sales"],
        chart: {
            type: "donut",
            height: 270,
            offsetY: 20,
            fontFamily: "inherit",
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            width: 0,
        },
        plotOptions: {
            pie: {
                expandOnClick: true,
                donut: {
                    size: "88",
                    labels: {
                        show: false,
                        name: {
                            show: true,
                            offsetY: 7,
                        },
                        value: {
                            show: false,
                        },
                        total: {
                            show: false,
                            color: "#a1aab2",
                            fontSize: "13px",
                            label: "Our Visitor",
                        },
                    },
                },
            },
        },
        colors: ["#edf1f5", "var(--bs-primary)", "var(--bs-success)", "var(--bs-secondary)"],
        tooltip: {
            show: true,
            fillSeriesColor: false,
        },
        legend: {
            show: false,
        },
        responsive: [
            {
                breakpoint: 1025,
                options: {
                    chart: {
                        width: 250,
                    },
                },
            },
            {
                breakpoint: 769,
                options: {
                    chart: {
                        height: 270,
                        width: "100%",
                    },
                },
            },
            {
                breakpoint: 426,
                options: {
                    chart: {
                        height: 250,
                        offsetX: -20,
                        width: 250,
                    },
                },
            },
        ],
    };

    var chart_pie_donut = new ApexCharts(
        document.querySelector("#sales-of-the-month"),
        option_Sales_of_the_Month
    );
    chart_pie_donut.render();



    // -----------------------------------------------------------------------
    // Income of the year chart
    // -----------------------------------------------------------------------

    var options_Income_of_the_Year = {
        series: [
            {
                name: "Growth ",
                data: [5, 4, 3, 7, 5, 10, 3],
            },
            {
                name: "Net ",
                data: [3, 2, 9, 5, 4, 6, 4],
            },
        ],
        chart: {
            fontFamily: "inherit",
            type: "bar",
            height: 315,
            offsetY: 10,
            toolbar: {
                show: false,
            },
        },
        grid: {
            show: true,
            strokeDashArray: 3,
            borderColor: "rgba(0,0,0,0.1)",
            xaxis: {
                lines: {
                    show: true,
                },
            },
        },
        colors: ["var(--bs-primary)", "var(--bs-success)"],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "50%",
                borderRadius: 5,
                endingShape: "rounded",
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 5,
            colors: ["transparent"],
        },
        xaxis: {
            type: "category",
            categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
            tickAmount: "16",
            tickPlacement: "on",
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            labels: {
                style: {
                    colors: "#a1aab2",
                },
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: "#a1aab2",
                },
            },
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            theme: "dark",
        },
        legend: {
            show: false,
        },
    };

    var chart_column_basic = new ApexCharts(
        document.querySelector("#income-of-the-year"),
        options_Income_of_the_Year
    );
    chart_column_basic.render();
});
