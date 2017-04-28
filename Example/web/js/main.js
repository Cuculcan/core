var CORE = CORE || {};
CORE.namespace = function (ns_string) {

    var parts = ns_string.split('.');
    var parent = CORE;


    if (parts[0] === "CORE") {
        parts = parts.splice(1);
    }

    for (var i = 0; i < parts.length; i++) {

        if (typeof parent[parts[i]] == "undefined") {
            parent[parts[i]] = {}
        }
        parent = parent[parts[i]];
    }
    return parent;
};
