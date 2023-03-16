/* Global ApexChart settings */
if (localStorage.darkMode === "true") {
	/* Dark mode */
	window.Apex = {
		theme: {
			mode: "dark",
		},
		chart: {
			foreColor: "#6a778f",
			background: "transparent",
			toolbar: {
				show: false,
			},
			zoom: {
				enabled: false,
			},
		},
		stroke: {
			width: 3,
			curve: "smooth",
		},
		dataLabels: {
			enabled: false,
		},
		legend: {
			position: "bottom",
			offsetY: 10,
			itemMargin: {
				horizontal: 6,
				vertical: 6,
			},
		},
		tooltip: {
			theme: "dark",
		},
		grid: {
			borderColor: "#535A6C",
			strokeDashArray: 2,
		},
		xaxis: {
			tooltip: {
				enabled: false,
			},
			axisBorder: {
				show: false,
			},
			axisTicks: {
				show: false,
			},
		},
	}
} else {
	/* Light mode */
	window.Apex = {
		chart: {
			foreColor: "#64748b",
			background: "transparent",
			toolbar: {
				show: false,
			},
			zoom: {
				enabled: false,
			},
		},
		stroke: {
			width: 3,
			curve: "smooth",
		},
		dataLabels: {
			enabled: false,
		},
		legend: {
			position: "bottom",
			offsetY: 10,
			itemMargin: {
				horizontal: 6,
				vertical: 6,
			},
		},
		grid: {
			strokeDashArray: 2,
		},
		xaxis: {
			tooltip: {
				enabled: false,
			},
			axisBorder: {
				show: false,
			},
			axisTicks: {
				show: false,
			},
		},
	}
}