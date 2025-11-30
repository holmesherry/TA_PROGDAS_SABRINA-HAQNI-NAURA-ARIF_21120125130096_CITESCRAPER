<?php
// citationtools.php – landing khusus Citate Tools (2 kolom seperti sketch)
?>
<div class="tools-landing">

    <div class="tools-heading">
        <h1 class="tools-script-title">Citate Tools</h1>
    </div>

    <!-- CREATE -->
    <section class="tools-section tools-left">
        <h2 class="tools-section-title">Create Citation</h2>
        <p class="tools-section-text">
             Create Citation is a tool designed to help users generate academic references accurately and
 efficiently based on bibliographic information they provide. By entering key details such as
 author name, publication title, journal or publisher, year, volume, and page numbers, the system
 automatically formats the reference into a selected citation style such as IEEE, APA, MLA,
 Chicago, Harvard, AMA, CSE, or Bluebook. This feature eliminates manual formatting errors and
 makes the citation process faster and more reliable for students, researchers, and academic
 writers.
        </p>

        <div class="tools-btn-wrapper">
            <a href="<?php echo mklink(['page' => 'create', 'menu' => 'tools']); ?>" 
               class="btn-cta tools-big-btn">
                Create Citation
            </a>
        </div>
    </section>

    <!-- CONVERT -->
    <section class="tools-section tools-right">
        <h2 class="tools-section-title">Convert Citation</h2>
        <p class="tools-section-text-right">
            Convert Citation allows users to transform an existing citation into a different citation style
 without rewriting it from scratch. Users simply paste any completed reference, and the system
 analyzes and reformats it according to the chosen academic style. Whether converting from
 APA to IEEE or from Mendeley format to Chicago style, this tool ensures accuracy and
 consistency across documents, supporting seamless formatting transitions for professional
 papers, reports, and research publishing.
        </p>

        <div class="tools-btn-wrapper">
            <a href="<?php echo mklink(['page' => 'convert', 'menu' => 'tools']); ?>" 
               class="btn-cta tools-big-btn">
                Convert Citation
            </a>
        </div>
    </section>

</div>
