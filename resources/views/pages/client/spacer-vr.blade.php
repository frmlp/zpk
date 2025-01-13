@extends('layouts.vr-tour')

@section('title', 'Spacer wirtualny')

@section('css_files')
    <link rel="stylesheet" href="vendor/reset.min.css">
    <link rel="stylesheet" href="css/spacer-style.css">
@endsection

@section('content')
    <div id="pano"></div>

    <div id="mapsContainer"></div>

    <div id="sceneList">
        <ul class="scenes">

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="0-s2">
                <li class="text">Punkt startowy S2</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="1-65">
                <li class="text">Punkt kontrolny #65</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="2-60">
                <li class="text">Punkt kontrolny #60</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="3-68">
                <li class="text">Punkt kontrolny #68</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="4-51">
                <li class="text">Punkt kontrolny #51</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="5-71">
                <li class="text">Punkt kontrolny #71</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="6-59">
                <li class="text">Punkt kontrolny #59</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="7-53">
                <li class="text">Punkt kontrolny #53</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="8-67">
                <li class="text">Punkt kontrolny #67</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="9-50">
                <li class="text">Punkt kontrolny #50</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="10-49">
                <li class="text">Punkt kontrolny #49</li>
            </a>

            <a href="javascript:void(0)" class="scene link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" data-id="11-57">
                <li class="text">Punkt kontrolny #57</li>
            </a>
        </ul>
    </div>

    <div id="titleBar">
        <h1 class="sceneName"></h1>
    </div>

    <a href="javascript:void(0)" id="autorotateToggle">
        <img class="icon off" src="img/play.png" />
        <img class="icon on" src="img/pause.png" />
    </a>

    <a href="javascript:void(0)" id="fullscreenToggle">
        <img class="icon off" src="img/fullscreen.png" />
        <img class="icon on" src="img/windowed.png" />
    </a>

    <a href="javascript:void(0)" id="sceneListToggle">
        <img class="icon off" src="img/expand.png" />
        <img class="icon on" src="img/collapse.png" />
    </a>

    <a href="javascript:void(0)" id="viewUp" class="viewControlButton viewControlButton-1">
        <img class="icon" src="img/up.png" />
    </a>
    <a href="javascript:void(0)" id="viewDown" class="viewControlButton viewControlButton-2">
        <img class="icon" src="img/down.png" />
    </a>
    <a href="javascript:void(0)" id="viewLeft" class="viewControlButton viewControlButton-3">
        <img class="icon" src="img/left.png" />
    </a>
    <a href="javascript:void(0)" id="viewRight" class="viewControlButton viewControlButton-4">
        <img class="icon" src="img/right.png" />
    </a>
    <a href="javascript:void(0)" id="viewIn" class="viewControlButton viewControlButton-5">
        <img class="icon" src="img/plus.png" />
    </a>
    <a href="javascript:void(0)" id="viewOut" class="viewControlButton viewControlButton-6">
        <img class="icon" src="img/minus.png" />
    </a>

@endsection

@section('js_files')
    <script src="vendor/screenfull.min.js" ></script>
    <script src="vendor/bowser.min.js" ></script>
    <script src="vendor/marzipano.js" ></script>
    <script src="js/spacer-data.js" ></script>
    <script src="js/spacer.js"></script>
@endsection