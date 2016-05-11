cmake_minimum_required(VERSION 2.8)

function (deps_path library type)
    execute_process (COMMAND "php" "${Deps_DIR}/deps.php" "${type}" "${library}"
        OUTPUT_VARIABLE RESULT)
    string(REPLACE ":" ";" RESULTLIST ${RESULT})
    set (PATHES)
    foreach (RESULTPATH ${RESULTLIST})
        string(STRIP ${RESULTPATH} RESULTPATH)
        if (NOT "${RESULTPATH}" STREQUAL "")
            set (PATHES ${PATHES} ${RESULTPATH})
        endif ()
    endforeach ()
    set (PATHES ${PATHES} PARENT_SCOPE)
endfunction ()

function (deps_add_library library)
    # Adding includes
    deps_path(${library} "includes")
    include_directories (${PATHES})

    # Adding links
    deps_path(${library} "links")
    list(APPEND DEPS_LIBRARIES ${PATHES})
    set(DEPS_LIBRARIES ${DEPS_LIBRARIES} PARENT_SCOPE)
endfunction ()
