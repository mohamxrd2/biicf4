const persons = [
    { name: "John", age: 30 },
    { name: "Jane", age: 25 },
    { name: "Bob", age: 40 },
    { name: "Alice", age: 35 },
];

const searchinput = document.getElementById("searchinput");

searchinput.addEventListener("keyup", function () {
    const input = searchinput.value;

    const result = persons.filter((item) =>
        item.name.toLocaleLowerCase().includes(input.toLocaleLowerCase())
    );

    let suggestion = "";

    if (input != "") {
        result.forEach(resultItem =>
            suggestion +=`
        <div class="suggestion">${resultItem.name}</div>
        `
        )
    }

    document.getElementById("suggestions").innerHTML = suggestion;
});
