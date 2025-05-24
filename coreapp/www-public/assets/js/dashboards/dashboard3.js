// -------------------------------------------------------------------------------------------------------------------------------------------
// Dashboard 3 : Chart Init Js
// -------------------------------------------------------------------------------------------------------------------------------------------
$(function () {
  "use strict";
  // -----------------------------------------------------------------------
  // Revenue Statistics
  // -----------------------------------------------------------------------
  var product_calculation = {
    series: [
      { name: "Inbound Calls", data: [65, 80, 80, 60, 60, 45, 45, 80, 80, 70, 70, 90, 90, 80, 80, 80, 60, 60, 50] },
      { name: "Outbound Calls", data: [90, 110, 110, 95, 95, 85, 85, 95, 95, 115, 115, 100, 100, 115, 115, 95, 95, 85, 85] },
    ],
    chart: { fontFamily: "inherit", type: "area", height: 327, toolbar: { show: !1 } },
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
    document.querySelector("#product-calculation"),
    product_calculation
  );
  chart_area_spline.render();




  // =====================================
  // total-profit
  // =====================================
  var options = {
    chart: {
      id: "total-profit",
      type: "area",
      height: 75,
      sparkline: {
        enabled: true,
      },
      group: "total-profit",
      fontFamily: "inherit",
      foreColor: "#adb0bb",
    },
    series: [
      {
        name: '',
        color: "var(--bs-white)",
        data: [50, 150, 110, 240, 200],
      },
    ],
    stroke: {
      curve: "smooth",
      width: 1.5,
    },

    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 0,
        inverseColors: false,
        opacityFrom: 0.20,
        opacityTo: 0,
        stops: [0, 100],
      },
    },

    markers: {
      size: 0,
    },
    tooltip: {
      theme: "dark",
      fixed: {
        enabled: true,
        position: "right",
      },
      x: {
        show: false,
      },
    },
  };
  new ApexCharts(document.querySelector("#total-profit"), options).render();





  // =====================================
  // total-profit 2
  // =====================================
  var options = {
    chart: {
      id: "total-profit2",
      type: "area",
      height: 75,
      sparkline: {
        enabled: true,
      },
      group: "total-profit2",
      fontFamily: "inherit",
      foreColor: "#adb0bb",
    },
    series: [
      {
        name: '',
        color: "var(--bs-white)",
        data: [0, 150, 110, 240, 200, 200, 300, 200],
      },
    ],
    stroke: {
      curve: "smooth",
      width: 1.5,
    },

    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 0,
        inverseColors: false,
        opacityFrom: 0.20,
        opacityTo: 0,
        stops: [0, 100],
      },
    },

    markers: {
      size: 0,
    },
    tooltip: {
      theme: "dark",
      fixed: {
        enabled: true,
        position: "right",
      },
      x: {
        show: false,
      },
    },
  };
  new ApexCharts(document.querySelector("#total-profit2"), options).render();


  // =====================================
  // Coverage chart   
  // =====================================


  var coverage = {
    color: "#adb5bd",
    series: [30, 10, 60],
    labels: ["2022", "2021", "2020"],
    chart: {
      height: 120,
      type: "donut",
      fontFamily: "inherit",
      foreColor: "#adb0bb",
    },
    plotOptions: {
      pie: {
        startAngle: 0,
        endAngle: 360,
        donut: {
          size: '80%',
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
    colors: ["#004670", "#00395C", "#005C94"],

    tooltip: {
      theme: "dark",
      fillSeriesColor: false,
    },
  };

  var chart = new ApexCharts(document.querySelector("#coverage"), coverage);
  chart.render();



  // =====================================
  // Pending Tasks
  // =====================================
  var options = {
    chart: {
      id: "pending-tasks",
      type: "area",
      height: 75,
      sparkline: {
        enabled: true,
      },
      group: "pending-tasks",
      fontFamily: "inherit",
      foreColor: "#adb0bb",
    },
    series: [
      {
        name: '',
        color: "var(--bs-white)",
        data: [15, 25, 40, 50, 20],
      },
    ],
    stroke: {
      curve: "smooth",
      width: 1.5,
    },

    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 0,
        inverseColors: false,
        opacityFrom: 0.20,
        opacityTo: 0,
        stops: [0, 100],
      },
    },

    markers: {
      size: 0,
    },
    tooltip: {
      theme: "dark",
      fixed: {
        enabled: true,
        position: "right",
      },
      x: {
        show: false,
      },
    },
  };
  new ApexCharts(document.querySelector("#pending-tasks"), options).render();






});
