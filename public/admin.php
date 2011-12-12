<html>
<head>
    <title>ZF TOTS Admin</title>
    <link rel="stylesheet" type="text/css" href="assets/ext/resources/css/ext-all.css">
    <script type="text/javascript" src="assets/ext/ext-all-debug.js"></script>
    <script type="text/javascript">
    Ext.application(
    {
        name: 'HelloExt',
        launch: function()
        {
            Ext.create('Ext.container.Viewport',
            {
                layout: 'border',
                defaults: {
                    collapsible: true,
                    split: true,
                    bodyStyle: 'padding:15px'
                },
                items: [
                {
                    id: 'nav-panel',
                    title: 'Details',
                    region: 'west',
                    width: 175,
                    minSize: 100,
                    maxSize: 250,
                    bodyStyle: 'padding-bottom:15px;background:#eee;',
                    autoScroll: true,
                    html: '<p class="details-info">Choose Items here</p>'
                }
                ,{
                    id: 'intro-panel',
                    title: 'Histories',
                    height: 150,
                    minSize: 75,
                    maxSize: 250,
                    region: 'south',
                    bodyStyle: 'padding-bottom:15px;background:#eee;',
                    autoScroll: true,
                    html: '<p class="details-info">Histories and so on</p>'
                }
                ,{
                    id: 'details-panel',
                    title: 'Details',
                    region: 'center',
                         collapsible: false,
                    bodyStyle: 'padding-bottom:15px;background:#eee;',
                    autoScroll: true,
                    html: '<p class="details-info">Data Grid and so on</p>'
                }
                ]
            });
        }
    });
    </script>
</head>
<body></body>
</html>