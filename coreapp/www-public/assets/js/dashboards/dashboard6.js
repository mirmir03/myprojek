// -------------------------------------------------------------------------------------------------------------------------------------------
// Dashboard 6 : Chart Init Js
// -------------------------------------------------------------------------------------------------------------------------------------------
$(function () {
    "use strict";

    // -----------------------------------------------------------------------
    // doughnut chart option
    // -----------------------------------------------------------------------

    var option_Visit_Source = {
        labels: ["Desktop", "Mobile", "Other", "Tablet"],
        series: [5.5, 3.5, 3, 2],
        chart: {
            type: "donut",
            height: 250,
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
                    size: "86%",
                    labels: {
                        show: true,
                        name: {
                            show: false,
                            offsetY: 7,
                        },
                        value: {
                            show: true,
                            fontSize: "20px",
                        },
                        total: {
                            show: false,
                            color: "#a1aab2",
                            fontSize: "13px",
                            label: "Visits",
                        },
                    },
                },
            },
        },
        colors: ["var(--bs-secondary)", "var(--bs-primary)", "#dadada", "var(--bs-danger)"],
        tooltip: {
            show: true,
            fillSeriesColor: false,
        },
        legend: {
            show: false,
        },
        responsive: [
            {
                breakpoint: 426,
                options: {
                    chart: {
                        height: 230,
                        width: 200,
                    },
                },
            },
        ],
    };

    var chart_pie_donut = new ApexCharts(
        document.querySelector("#visit-source"),
        option_Visit_Source
    );
    chart_pie_donut.render();

    // -----------------------------------------------------------------------
    // Realtime chart
    // -----------------------------------------------------------------------

    var options_Android_Vs_iOS = {
        series: [
            {
                name: "Growth ",
                data: [
                    8, 1, 1, 10, 11, 6, 12, 14, 21, 15, 21, 24, 28, 23, 34, 38, 41, 47,
                ],
            },
            {
                name: "Loss ",
                data: [
                    11, 4, 3, 14, 9, 10, 18, 15, 24, 17, 19, 26, 31, 26, 37, 41, 46, 51,
                ],
            },
        ],
        chart: {
            height: 350,
            type: "area",
            stacked: false,
            fontFamily: "inherit",
            zoom: {
                enabled: false,
            },
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true,
            },
        },
        colors: ["rgba(38, 198, 218, 0.7)", "rgba(38, 198, 218, 0.1)"],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: false,
        },
        markers: {
            size: 2,
            strokeColors: "transparent",
            colors: "#26c6da",
        },
        fill: {
            type: "solid",
            colors: ["rgba(38, 198, 218, 0.6)", "rgba(38, 198, 218, 0.3)"],
            opacity: 1,
        },
        grid: {
            show: true,
            strokeDashArray: 3,
            borderColor: "rgba(0,0,0,0.1)",
        },
        xaxis: {
            labels: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
        },
        legend: {
            show: false,
        },
        tooltip: {
            theme: "dark",
            marker: {
                show: true,
            },
        },
    };

    var chart_line_overview = new ApexCharts(
        document.querySelector("#android-vs-ios"),
        options_Android_Vs_iOS
    );
    chart_line_overview.render();
    


    var option_Bandwidth_Usage = {
        series: [
            {
                name: "",
                data: [2, 3, 8, 10, 8, 12, 2, 8, 2, 4, 1, 10, 8, 12, 10],
            },
        ],
        chart: {
            type: "bar",
            fontFamily: "inherit",
            height: 120,
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true,
            },
        },
        colors: ["rgba(255, 255, 255, 0.3)"],
        grid: {
            show: false,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                startingShape: "flat",
                endingShape: "flat",
                columnWidth: "100%",
                barHeight: "100%",
                borderRadius: 5,
                endingShape: "rounded",
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 4,
            colors: ["transparent"],
        },
        xaxis: {
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            labels: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                show: false,
            },
        },
        axisBorder: {
            show: false,
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            theme: "dark",
            style: {
                fontSize: "12px",
                fontFamily: "inherit",
            },
            x: {
                show: false,
            },
            y: {
                formatter: undefined,
            },
        },
    };

    var chart_column_basic = new ApexCharts(
        document.querySelector("#bandwidth-usage"),
        option_Bandwidth_Usage
    );
    chart_column_basic.render();



    // -----------------------------------------------------------------------
    // Download count
    // -----------------------------------------------------------------------

    var option_Download_Count_1 = {
        series: [
            {
                name: "",
                data: [2, 3, 8, 10, 8, 12, 2, 8, 2, 4, 1, 10, 8, 12, 10],
            },
        ],
        chart: {
            type: "bar",
            fontFamily: "inherit",
            height: 120,
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true,
            },
        },
        colors: ["rgba(255, 255, 255, 0.3)"],
        grid: {
            show: false,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                startingShape: "flat",
                endingShape: "flat",
                columnWidth: "100%",
                barHeight: "100%",
                borderRadius: 5,
                endingShape: "rounded",
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 4,
            colors: ["transparent"],
        },
        xaxis: {
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            labels: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                show: false,
            },
        },
        axisBorder: {
            show: false,
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            theme: "dark",
            style: {
                fontSize: "12px",
                fontFamily: "inherit",
            },
            x: {
                show: false,
            },
            y: {
                formatter: undefined,
            },
        },
    };

    var chart_column_basic = new ApexCharts(
        document.querySelector("#download-count"),
        option_Download_Count_1
    );
    chart_column_basic.render();

    // -----------------------------------------------------------------------
    // Download count
    // -----------------------------------------------------------------------

    var options_Download_Count_2 = {
        series: [
            {
                name: "Premium ",
                data: [5, 4, 3, 7, 5, 10, 3],
            },
            {
                name: "Free ",
                data: [3, 2, 9, 5, 4, 6, 4],
            },
        ],
        chart: {
            fontFamily: "inherit",
            type: "bar",
            height: 300,
            offsetY: 10,
            toolbar: {
                show: false,
            },
        },
        grid: {
            show: true,
            strokeDashArray: 3,
            borderColor: "rgba(0,0,0,0.1)",
        },
        colors: ["var(--bs-primary)", "var(--bs-success)"],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "20%",
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
            categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            tickAmount: "16",
            tickPlacement: "on",
            axisTicks: {
                show: false,
            },
            axisBorder: {
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
        document.querySelector("#download-count-2"),
        options_Download_Count_2
    );
    chart_column_basic.render();
});
