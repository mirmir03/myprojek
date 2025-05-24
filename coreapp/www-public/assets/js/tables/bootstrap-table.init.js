$(function () {
  $("#table").bootstrapTable();

  // custom table
  $("#custom-table").bootstrapTable({
    data: [
      {
        name: "Mike",
        image: "https://robohash.org/68.186.255.198.png",
        follower: 10,
        following: 123,
        snippets: 12,
      },
      {
        name: "Tim",
        image: "https://robohash.org/18.146.255.198.png",
        follower: 70,
        following: 23,
        snippets: 42,
      },
      {
        name: "Jorge",
        image: "https://robohash.org/63.186.255.198.png",
        follower: 40,
        following: 74,
        snippets: 567,
      },
      {
        name: "Luke",
        image: "https://robohash.org/68.186.155.198.png",
        follower: 550,
        following: 23,
        snippets: 52,
      },
      {
        name: "Mike",
        image: "https://robohash.org/68.186.255.198.png",
        follower: 10,
        following: 123,
        snippets: 12,
      },
      {
        name: "Tim",
        image: "https://robohash.org/18.146.255.198.png",
        follower: 70,
        following: 23,
        snippets: 42,
      },
      {
        name: "Jorge",
        image: "https://robohash.org/63.186.255.198.png",
        follower: 40,
        following: 74,
        snippets: 567,
      },
      {
        name: "Luke",
        image: "https://robohash.org/68.186.155.198.png",
        follower: 550,
        following: 23,
        snippets: 52,
      },
      {
        name: "Mike",
        image: "https://robohash.org/68.186.255.198.png",
        follower: 10,
        following: 123,
        snippets: 12,
      },
      {
        name: "Tim",
        image: "https://robohash.org/18.146.255.198.png",
        follower: 70,
        following: 23,
        snippets: 42,
      },
      {
        name: "Jorge",
        image: "https://robohash.org/63.186.255.198.png",
        follower: 40,
        following: 74,
        snippets: 567,
      },
      {
        name: "Luke",
        image: "https://robohash.org/68.186.155.198.png",
        follower: 550,
        following: 23,
        snippets: 52,
      },
      {
        name: "Mike",
        image: "https://robohash.org/68.186.255.198.png",
        follower: 10,
        following: 123,
        snippets: 12,
      },
      {
        name: "Tim",
        image: "https://robohash.org/18.146.255.198.png",
        follower: 70,
        following: 23,
        snippets: 42,
      },
      {
        name: "Jorge",
        image: "https://robohash.org/63.186.255.198.png",
        follower: 40,
        following: 74,
        snippets: 567,
      },
      {
        name: "Luke",
        image: "https://robohash.org/68.186.155.198.png",
        follower: 550,
        following: 23,
        snippets: 52,
      },
    ],
  });
});

function customViewFormatter(data) {
  var template = $("#profileTemplate").html();
  var view = "";
  $.each(data, function (i, row) {
    view += template
      .replace("%NAME%", row.name)
      .replace("%IMAGE%", row.image)
      .replace("%FOLLOWER%", row.follower)
      .replace("%FOLLOWING%", row.following)
      .replace("%SNIPPETS%", row.snippets);
  });

  return `<div class="row mx-0">${view}</div>`;
}

// export
var $table = $("#export-table");

$(function () {
  $("#toolbar")
    .find("select")
    .change(function () {
      $table.bootstrapTable("destroy").bootstrapTable({
        exportDataType: $(this).val(),
        exportTypes: ["json", "xml", "csv", "txt", "sql", "excel", "pdf"],
        columns: [
          {
            field: "state",
            checkbox: true,
            visible: $(this).val() === "selected",
          },
          {
            field: "id",
            title: "ID",
          },
          {
            field: "name",
            title: "Item Name",
          },
          {
            field: "price",
            title: "Item Price",
          },
        ],
      });
    })
    .trigger("change");

  // print
  $("#print-table").bootstrapTable();

  // toolbar
  $("#toolbar-table").bootstrapTable();

  // multi sorting
  $("#multiple-sort-table").bootstrapTable();

  // page jump to
  $("#page-jump-table").bootstrapTable();
});
