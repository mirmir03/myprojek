// -------------------------------------------------------------------------------------------------------------------------------------------
// Dashboard 4 : Chart Init Js
// -------------------------------------------------------------------------------------------------------------------------------------------
$(function () {
    "use strict";

    var option_unique_visit = {
        series: [
            {
                name: "",
                data: [4, 5, 2, 10, 9, 12, 4, 9],
            },
        ],
        chart: {
            type: "bar",
            fontFamily: "inherit",
            height: 70,
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true,
            },
        },
        colors: ["var(--bs-success)"],
        grid: {
            show: false,
        },
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
        document.querySelector("#unique-visit"),
        option_unique_visit
    );
    chart_column_basic.render();


    var option_total_visit = {
        series: [
            {
                name: "",
                data: [2, 5, 6, 10, 9, 12, 4, 9],
            },
        ],
        chart: {
            type: "bar",
            fontFamily: "inherit",
            height: 70,
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true,
            },
        },
        colors: ["var(--bs-secondary)"],
        grid: {
            show: false,
        },
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
        document.querySelector("#total_visit"),
        option_total_visit
    );
    chart_column_basic.render();




    var option_bounce_rate = {
        series: [
            {
                name: "",
                data: [2, 5, 6, 10, 9, 12, 4, 9],
            },
        ],
        chart: {
            type: "bar",
            fontFamily: "inherit",
            height: 70,
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true,
            },
        },
        colors: ["var(--bs-primary)"],
        grid: {
            show: false,
        },
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
        document.querySelector("#bounce-rate"),
        option_bounce_rate
    );
    chart_column_basic.render();


    var option_page_views = {
        series: [
            {
                name: "",
                data: [2, 5, 6, 10, 9, 12, 4, 9],
            },
        ],
        chart: {
            type: "bar",
            fontFamily: "inherit",
            height: 70,
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true,
            },
        },
        colors: ["var(--bs-danger)"],
        grid: {
            show: false,
        },
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
        document.querySelector("#page_views"),
        option_page_views
    );
    chart_column_basic.render();


    // -----------------------------------------------------------------------
    // Total revenue chart
    // -----------------------------------------------------------------------

    var options_Total_Revenue = {
        series: [
            {
                name: "2024",
                data: [
                    800000, 1200000, 1400000, 1300000, 1200000, 1400000, 1300000, 1300000,
                    1200000,
                ],
            },
            {
                name: "2023",
                data: [
                    200000, 400000, 500000, 300000, 400000, 500000, 300000, 300000,
                    400000,
                ],
            },
            {
                name: "2022",
                data: [
                    100000, 200000, 400000, 600000, 200000, 400000, 600000, 600000,
                    200000,
                ],
            },
        ],
        chart: {
            fontFamily: "inherit",
            type: "bar",
            height: 342,
            stacked: true,
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: true,
            },
        },
        grid: {
            borderColor: "rgba(0,0,0,0.1)",
            strokeDashArray: 3,
        },
        colors: ["#0f8edd", "#11a0f8", "#51bdff"],
        responsive: [
            {
                breakpoint: 480,
                options: {
                    legend: {
                        position: "bottom",
                        offsetX: -10,
                        offsetY: 0,
                    },
                },
            },
        ],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "25%",
                borderRadius: 8,
                endingShape: "rounded",
            },
        },
        dataLabels: {
            enabled: false,
        },
        xaxis: {
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            categories: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sept",
            ],
            labels: {
                style: {
                    colors: "#a1aab2",
                },
            },
        },
        yaxis: {
            tickAmount: 10,
            labels: {
                style: {
                    colors: "#a1aab2",
                },
            },
        },
        tooltip: {
            theme: "dark",
        },
        legend: {
            show: false,
        },
        fill: {
            opacity: 1,
        },
    };

    var chart_column_stacked = new ApexCharts(
        document.querySelector("#total-revenue"),
        options_Total_Revenue
    );
    chart_column_stacked.render();



    // -----------------------------------------------------------------------
    // Sales difference
    // -----------------------------------------------------------------------


    var options = {
        color: "#adb5bd",
        series: [12, 88],
        labels: ["12%", "88%"],
        chart: {
            type: "donut",
            height: 140,
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
                            fontSize: '16px',
                            fontWeight: "500",
                            label: '12%',
                        },
                    },
                },
            },
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

    var chart = new ApexCharts(document.querySelector("#sales-difference"), options);
    chart.render();



    // -----------------------------------------------------------------------
    // Sales Prediction
    // -----------------------------------------------------------------------

    var options = {
        color: "#adb5bd",
        series: [46, 54],
        labels: ["54%", "46%"],
        chart: {
            type: "donut",
            height: 140,
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
                            fontSize: '16px',
                            fontWeight: "500",
                            label: '54%',
                        },
                    },
                },
            },
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
        colors: ["#9fa0a5", "var(--bs-success)",],

        tooltip: {
            theme: "dark",
            fillSeriesColor: false,
        },
    };

    var chart = new ApexCharts(document.querySelector("#sales-prediction"), options);
    chart.render();



    // -----------------------------------------------------------------------
    // world map
    // -----------------------------------------------------------------------
    jQuery("#visitfromworld").vectorMap({
        map: "world_mill_en",
        backgroundColor: "#fff",
        borderColor: "#ccc",
        borderOpacity: 0.9,
        borderWidth: 1,
        zoomOnScroll: false,
        color: "#ddd",
        regionStyle: {
            initial: {
                fill: "rgba(0,0,0,0.1)",
            },
        },
        markerStyle: {
            initial: {
                r: 8,
                fill: "#55ce63",
                "fill-opacity": 1,
                stroke: "#000",
                "stroke-width": 0,
                "stroke-opacity": 1,
            },
        },
        enableZoom: true,
        hoverColor: "#79e580",
        markers: [
            {
                latLng: [21.0, 78.0],
                name: "India : 9347",
                style: { fill: "#55ce63" },
            },
            {
                latLng: [-33.0, 151.0],
                name: "Australia : 250",
                style: { fill: "#02b0c3" },
            },
            {
                latLng: [36.77, -119.41],
                name: "USA : 250",
                style: { fill: "#11a0f8" },
            },
            {
                latLng: [55.37, -3.41],
                name: "UK   : 250",
                style: { fill: "#745af2" },
            },
            {
                latLng: [25.2, 55.27],
                name: "UAE : 250",
                style: { fill: "#ffbc34" },
            },
        ],
        hoverOpacity: null,
        normalizeFunction: "linear",
        scaleColors: ["#fff", "#ccc"],
        selectedColor: "#c9dfaf",
        selectedRegions: [],
        showTooltip: true,
        onRegionClick: function (element, code, region) {
            var message =
                'You clicked "' +
                region +
                '" which has the code: ' +
                code.toUpperCase();
            alert(message);
        },
    });



});  