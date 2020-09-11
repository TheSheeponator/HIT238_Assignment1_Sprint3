<?php
    require "header.php";
?>
    
    <main style="flex-direction: column;">
        <script src="./scripts/jquery.js"></script>
        <script src="./scripts/jquery.imagemapster.min.js"></script>
        <img id="controlImg" src="./img/House Plan.png" alt="Intractable house plan" name="nav" usemap="#nav">
        <map name="nav" id="nav">
            <area target="" alt="FrontL" title="Front Lawn" href="#" onclick="mapClick('FL')" key='FL' shape="poly" coords="988,2235,988,2195,986,2145,984,2112,982,2080,978,2038,968,1994,958,1933,939,1880,919,1834,891,1773,870,1732,838,1675,811,1634,778,1587,748,1545,731,1522,707,1539,328,1039,289,1048,261,1059,233,1073,202,1092,179,1116,168,1135,164,1154,163,1179,161,1206,173,1680,173,1692,175,1713,176,1733,179,1757,184,1782,189,1808,198,1834,210,1857,224,1877,247,1904,270,1921,298,1938,326,1955,363,1977,403,1994,440,2007,481,2022,528,2042,577,2057,638,2076,702,2096,748,2109,778,2120,804,2129,831,2138,855,2146,879,2156,904,2169,931,2185,948,2203,953,2222,957,2234">
            <area target="" alt="FrontG" title="Front Garden" href="#" onclick="mapClick('FG')" key='FG'  shape="poly" coords="955,2235,944,2199,916,2177,863,2153,800,2130,706,2097,634,2076,521,2039,441,2011,411,2003,390,1993,369,1982,343,1969,319,1955,296,1944,278,1932,264,1921,242,1904,226,1888,209,1867,195,1842,184,1815,177,1789,174,1762,170,1740,170,1718,168,1697,154,1231,155,1198,155,1181,156,1158,160,1134,169,1116,184,1098,201,1084,222,1070,246,1059,267,1049,286,1044,323,1034,207,885,35,884,35,2232">
            <area target="" alt="MediumL" title="MediumL Strip Lawn" href="#" onclick="mapClick('ML')" key='ML' shape="poly" coords="35,2301,35,2424,983,2427,983,2299">
            <area target="" alt="MediumL" title="MediumL Strip Lawn" href="#" onclick="mapClick('ML')" key='ML' shape="poly" coords="1465,2430,1185,2427,1185,2299,1464,2300">
            <area target="" alt="ShedL" title="Shed Lawn" href="#" onclick="mapClick('SL')" key='SL'  shape="poly" coords="1157,1922,1148,1886,1139,1853,1123,1809,1106,1764,1087,1722,1065,1674,1040,1628,1012,1581,979,1534,941,1479,888,1404,1254,1124,1375,1102,1375,1192,1465,1194,1465,1612,1273,1613,1271,1808,1274,1842,1250,1848,1230,1857,1213,1869,1195,1885,1179,1900">
            <area target="" alt="PoolL" title="Pool Lawn" href="#" onclick="mapClick('PL')" key='PL'  shape="poly" coords="1044,848,1054,841,1054,130,688,130,655,346,744,462,749,457,744,462">
            <area target="" alt="bbqG" title="BBQ Garden" href="#" onclick="mapClick('BG')" key='BG'  shape="poly" coords="96,880,36,878,35,130,680,129,649,347,532,432,622,552,521,628,502,607,398,254,100,260">
            <area target="" alt="ShedG" title="Shed Garden" href="#" onclick="mapClick('SG')" key='SG'  shape="poly" coords="1159,1930,1185,1906,1204,1888,1214,1880,1224,1872,1236,1865,1253,1856,1273,1849,1297,1846,1326,1843,1348,1843,1465,1842,1466,2235,1184,2232,1183,2173,1179,2083,1173,2020,1167,1969">
        </map>
        <script>
            $(document).ready(() => {
                // Sets up mapped image with the mapster library.
                $('#controlImg').mapster( {
                    fillcolor: 'bfbfbf80',
                    singleSelect: true,
                    mapKey: 'key',
                });

                $('#controlImg').mapster('resize', 0, (window.innerHeight*0.31));
            });
        </script>
        <table class="controlTable" id="controlTable">
            <tr class="colourChange" style="background-color: #808080">
                <th colspan="6">
                    <span id="title">--------</span>
                </th>
            </tr>
            <tr class="colourChange" style="background-color: #808080">
                <th>Start Time</th>
                <th>Finish Time</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Manual</th>
                <th>Auto</th>
            </tr>
            <tr style="background-color: #8080807e">
                <td id="st">00:00</td>
                <td id="ft">00:00</td>
                <td id="du">0</td>
                <td id="su">--</td>
                <td>
                    <button type="submit" id="manualON" disabled>ON</button>
                    <button type="submit" id="manualOFF" disabled>OFF</button>
                </td>
                <td>
                    <button type="submit" id="autoFormButton" disabled>AUTO</button>
                </td>
            </tr>
        </table>
        <div id="editResultMessageContainer"></div>
        <button class="editWindowButton" disabled>Edit</button>
        <div class="popupContainer">
            <div class="popupBackground"></div>
            <span class="helper"></span>
            <div class="popupBody">
                <div class="popupCloseButton">&times;</div>
                <form id="editForm">
                    <table>
                        <tbody>
                            <tr>
                                <td><label for="startTimeINP">Start Time: </label></td>
                                <td><input type="text" name="start" id="startTimeINP" class="editINP" maxlength="5" placeholder="00:00"></td>
                            </tr>
                            <tr>
                                <td><label for="finishTimeINP">Finish Time: </label></td>
                                <td><input type="text" name="finish" id="finishTimeINP" class="editINP" maxlength="5" placeholder="00:00"></td>
                            </tr>
                            <tr>
                                <td><label for="duration">Duration: </label></td>
                                <td><input type="text" id="duration" readonly></td>
                            </tr>
                                <td colspan="2">
                                    <p>Please enter times in 24-hour notation in the format: 'HH:MM'.</p>
                                    <button type="submit">Save</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </main>
    <script type="text/javascript" src="./scripts/control.js"></script>
    <script type="text/javascript" src="/scripts/dataManager.js"></script>
</html>