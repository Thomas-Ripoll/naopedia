/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


 $(document).ready(function () {
     
         document.querySelectorAll( '.awesome-ckeditor textarea' )
             .forEach(function(el){
                 el.removeAttribute('required');
                 ClassicEditor
                     .create( el )
                     .then( function (editor) {
                         console.log( editor );
                         var div = el.parentNode.querySelector('.ck-editor__editable');
                         div.style.backgroundColor = 'white'; 
                         div.style.minHeight = '300px';
                     } )
                     .catch( function (error) {
                         console.error( error );
                     } );
             });

     });