'use strict';
wp.blocks.registerBlockType('smp/simple-poll', {
    title: 'Simple Poll',
    category: 'design',
    icon: 'hammer',
    attributes: {
        selectPoll: { type: 'string' },
        selectLayout: { type: 'string' }
    },
    edit: function (props) {
        return React.createElement("form", {
            action: "#"
        }, /*#__PURE__*/React.createElement("h2", null, "Simple Poll"), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("h5", null, "Select Poll"), /*#__PURE__*/React.createElement("select", {
            name: "smp_poll_option",
            id: "smp__poll_options"
        }, /*#__PURE__*/React.createElement("option", {
            value: "option1"
        }, "option 1"), /*#__PURE__*/React.createElement("option", {
            value: "option1"
        }, "option 1"), /*#__PURE__*/React.createElement("option", {
            value: "option1"
        }, "option 1"), /*#__PURE__*/React.createElement("option", {
            value: "option1"
        }, "option 1"))), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("h5", null, "Select Poll"), /*#__PURE__*/React.createElement("select", {
            name: "poll_style",
            id: "smp__poll_style"
        }, /*#__PURE__*/React.createElement("option", {
            value: "grid"
        }, "Grid"), /*#__PURE__*/React.createElement("option", {
            value: "list"
        }, "List"))));
    },
    save: function (props) {
        return null;
    }
})