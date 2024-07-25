@extends('layout')

@section('content')
    <script src="https://unpkg.com/pdf-lib"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 style="text-transform: uppercase;">
                Ký tên
            </h1>
        </section>
        <div class="section">
            <div style="display: flex; flex-direction: row; height: calc(100vh - 100px);">
                <iframe id="pdf" style="width: 100%; height: 100%;border: none; background-color: #efefef; border-radius: 4px"></iframe>
                <div style="padding: 10px">
                    <label>Chọn PDF:</label>
                    <input name="file" type="file" id="file">

                    <div style="margin-top: 10px; margin-bottom: 5px">
                        <label>Ký ở đây</label>
                        <div style="border: 1px solid #ccc; background-color: #efefef">
                            <canvas></canvas>
                        </div>
                    </div>
                    <button id="sign-completed" class="mr-2">Xong</button>
                    <button id="sign-clear">Xóa</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#file').on('change', function () {
                createPdf();
            })
        })

        async function createPdf(signImage = null) {
            const url = 'https://pdf-lib.js.org/assets/with_update_sections.pdf'
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())

            const file = document.getElementById('file').files[0]

            const reader = new FileReader();
            reader.readAsArrayBuffer(file);
            reader.onload = async () => {
                const pdfDoc = await PDFLib.PDFDocument.load(reader.result);
                const pages = pdfDoc.getPages()
                const firstPage = pages[0]
                if (signImage) {
                    const pngImage = await pdfDoc.embedPng(signaturePad.toDataURL())

                    const form = pdfDoc.getForm();
                    form.getFields().map(field => {
                        var page = pdfDoc.findPageForAnnotationRef(field.ref);
                        console.log(page)
                        var widget = field.acroField.getWidgets()[0];
                        field.acroField.getWidgets().forEach(widget => {
                        })

                        const {width, height, x, y} = widget.getRectangle();
                        page.drawImage(pngImage, {
                            x: x, // firstPage.getWidth() / 2 - 150,
                            y: y, //  - pngDims.height,
                            width: height * 2,
                            height: height,
                        })

                        while (field.acroField.getWidgets().length) {
                            field.acroField.removeWidget(0);
                        }
                        form.removeField(field);
                    })
                }

                const pdfDataUri = await pdfDoc.saveAsBase64({dataUri: true});
                document.getElementById('pdf').src = pdfDataUri;
            };
        }

        const canvas = document.querySelector("canvas");

        const signaturePad = new SignaturePad(canvas, {
            // backgroundColor: '#efefef'
        });
        $('#sign-completed').click(function (e) {
            e.preventDefault();
            console.log(signaturePad.toDataURL()); // save image as PNG

            createPdf(signaturePad.toDataURL())
        })
        $('#sign-clear').click(function (e) {
            signaturePad.clear()
        })
    </script>
@endsection
