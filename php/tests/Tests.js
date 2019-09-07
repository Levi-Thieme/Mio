var testUrls = [];

function createTestArray(testData) {
    let allTests = [];
    for (let testName in testData) {
        let test = { name: testName, passed: testData[testName]};
        allTests.push(test);
    }
    return allTests;
}

/*
Retrieves the test results from each url in testUrls and displays the results.
*/
function runAllTests() {
    clearTestResults();
    let testPromises = getTestPromises().then(function(testData) {
        let allTests = [];
        testData.map(tests => {
            let parsedTests = JSON.parse(tests);
            for(let testName in parsedTests) {
                let test = { name: testName, passed: parsedTests[testName]};
                allTests.push(test);
            }
        });       
        displayTestResults(allTests);
        displayTestPassToFailRatio(allTests.filter(test=>test.passed).length, allTests.length);
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
            resolve(xhr.responseText);
        }
        else {
            reject("Failed to run tests at " + url);
        }
    }
    xhr.send();
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
        if (test.passed) {
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
        runTestsAsync(target.srcElement.innerText, 
            function(testData) { //accept
                clearTestResults();
                let tests = createTestArray(JSON.parse(testData));
                displayTestResults(tests);
                displayTestPassToFailRatio(tests.filter(test=>test.passed).length ,tests.length);
            },
            function(reason) { //reject
                alert(reason);
            }
        );
    });
});
