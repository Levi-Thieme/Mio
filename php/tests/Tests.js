var testUrls = [];

/*
Retrieves the test results from each url in testUrls and displays the results.
*/
function runAllTests() {
    clearTestResults();
    let startTime = performance.now();
    let testPromises = getTestPromises().then((allTestSuites)=> {
        allTestSuites = allTestSuites.flat();
        displayRuntime(startTime, performance.now());
        displayTestResults(allTestSuites);
        displayTestPassToFailRatio(allTestSuites.filter(test=>test.status).length, allTestSuites.length);
    }, function(reason) {
        alert(reason);
    });
}

function getTestPromises() {
    let testPromises = [];
    testUrls.map(url => {
        testPromises.push(new Promise(function(resolve, reject) {
            runTestsAsync(url, resolve, reject);
        }))
    });
    return Promise.all(testPromises);
}

/*
Sends a request for test results to the specified url,
and resolves or rejects based on the xhr.status.
*/
function runTestsAsync(url, resolve, reject) {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", url);
    xhr.setRequestHeader("Content-Type", "JSON");
    xhr.onload = function() {
        if (xhr.status === 200) {
            resolve(constructTestResults(xhr.responseText));
        }
        else {
            reject("Failed to run tests at " + url);
        }
    }
    xhr.send();
}

/*
Parses the responseText from runTestsAsync
*/
function parseResponseText(text) {
    let tests = "{}";
    let start = text.search("{");
    let end = text.search("}");
    if (start > 0) {
        let phpErrorMessage = text.slice(0, start - 1);
        console.log(phpErrorMessage);
        let div = document.createElement("div");
        div.innerHTML = phpErrorMessage;
        let row = document.createElement("tr");
        row.classList.add("failed");
        row.append(createElementWithTextContent("td", "A PHP Error Occurred"));
        let status = createElementWithTextContent("td", div.innerText);
        status.style.textAlign = "center";
        row.append(status);
        document.getElementById("testsTableBody").append(row);
    }
    try {
        tests = JSON.parse(text.slice(start, end + 1));
    } catch (e) {
        console.log(e);
        console.log(text);
    }
    return tests;
}

/*
Constructs an array of test result objects.
*/
function constructTestResults(responseText) {
    let tests = parseResponseText(responseText);
    let testResults = [];
    for (let test in tests) {
        testResults.push({ name: test, status: tests[test]})
    }
    return testResults;
}

/*
Removes the test result rows from testsTableBody.
*/
function clearTestResults() {
    let tableBody = document.getElementById("testsTableBody");
    while (tableBody.lastChild) {
        tableBody.removeChild(tableBody.lastChild);
    }
}

function displayTestPassToFailRatio(passed, totalTests) {
    document.getElementById("passToFailRatio").innerText = passed + "/" + totalTests + " Passed";
}

/*
Displays the test results in the testsTable.
*/
function displayTestResults(tests) {
    tests.map(test => {
        let row = document.createElement("tr");
        if (test.status) {
            row.classList.add("passed");
            row.append(createElementWithTextContent("td", test.name));
            let status = createElementWithTextContent("td", "Passed");
            status.style.textAlign = "center";
            row.append(status);
        }
        else {
            row.classList.add("failed");
            row.append(createElementWithTextContent("td", test.name));
            let status = createElementWithTextContent("td", "Failed");
            status.style.textAlign = "center";
            row.append(status);
        }
        document.getElementById("testsTableBody").append(row);
    });
}

function displayRuntime(startTime, endTime) {
    document.getElementById("runtime").innerText = (endTime - startTime).toFixed(2) + " ms";
}

/*
Retrieves all urls of tests from the server.
OnSuccess is a callback to handle the testUrls.
*/
function getTestUrls(onSuccess) {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "./GetTestUrls.php");
    xhr.setRequestHeader("Content-Type", "JSON");
    xhr.onload = function() {
        if (xhr.status === 200) {
            let urls = JSON.parse(xhr.responseText);
            onSuccess(urls);
        }
        else {
            alert("Failed to retrieve test urls from the server.");
        }
    }
    xhr.send();
}

/*
Creates an Element of type elementType with content as its textContent.
    <elementType> Content </elementType>
*/
function createElementWithTextContent(elementType, content) {
    let element = document.createElement(elementType);
    element.textContent = content;
    return element;
}

document.addEventListener("DOMContentLoaded", function() {
    //Add all test suites to the dropdown and set testUrls array.
    getTestUrls(function(data) {
        testUrls = data
        testUrls.map(url => document.getElementById("dropdownContent").append(createElementWithTextContent("a", url)));
    });
    document.getElementById("runAllTestsBtn").addEventListener("click", runAllTests);
    document.getElementById("dropdownContent").addEventListener("click", function(target) {
        clearTestResults();
        let startTime = performance.now();
        runTestsAsync(target.srcElement.innerText, 
            function(testData) { //accept
                displayRuntime(startTime, performance.now());
                displayTestResults(testData);
                displayTestPassToFailRatio(testData.filter(test=>test.status).length , testData.length);
            },
            function(reason) { //reject
                alert(reason);
            }
        );
    });
});
