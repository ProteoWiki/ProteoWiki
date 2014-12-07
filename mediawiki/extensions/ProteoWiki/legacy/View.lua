local p = {}

-- Get SMWChildren
function p.EntityChildren(frame)
    local entity = mw.text.trim(frame.args[1])
    local output = ""
    output = EntityIterate("SMWChildren", entity, 0, frame, output) 
    return output
end

-- Get SMWParent
function p.EntityParent(frame)
    local entity = mw.text.trim(frame.args[1])
    local output = ""
    output = EntityIterate("SMWParent", entity, 0, frame, output) 
    return output
end

function EntityIterate(func, entity, iter, frame, output)
    iter = iter + 2
    local parsercall = "{{#"..func..":"..entity.."|"..iter.."}}"
    local result = frame:preprocess(parsercall)
    -- If empty or request 
    if ( result ~= "" ) then
        if ( mw.ustring.find( result, "^Request") ) then
            output = output.."<p>"..result.."</p>"
        else
            output = output.."<p>"..result.."</p>"
            output = EntityIterate(func, entity, iter, frame, output)
        end
--]]
    end
    return output    
end

function p.EntityShowProps(frame)
    local pagetitle =  mw.text.trim(frame.args[1])
    local propsText = mw.text.trim(frame.args[2])
    local propsList = mw.text.split(propsText, ",") 
    
    local str = ""
    -- str = pagetitle..propsText.."\n"

    str = EntityReadProps(frame, pagetitle, propsList, str)
    str = str..EntityWriteProps(frame, pagetitle, propsList, "")

    return str
end

-- Function for looptemplating
function p.Loop_Template(frame) 

    local template =  mw.text.trim(frame.args[1])
    local currentpage =  frame:preprocess("{{FULLPAGENAME}}")

    local labelsText = GetRows(frame, template, 4)
    local propsText = GetRows(frame, template, 3)
    local paramsText = GetRows(frame, template, 2)

    local labelsList = mw.text.split(labelsText, ",") 
    local propsList = mw.text.split(propsText, ",") 
    local paramsList = mw.text.split(paramsText, ",") 

    local tablestr = ""

    -- Check if same length
    if #labelsList > 0 then
        if ( ( #labelsList +  #propsList ) == ( #propsList + #paramsList )  ) then
            tablestr = tablestr..'{| class="wikitable" \n'
            tablestr = tablestr..'! Label !! Value\n'
            for i,v in ipairs(propsList) do
                -- We get value from arguments
                local pframe = frame:getParent()
                local val = pframe:preprocess("{{{"..paramsList[i].."|}}}")
                if val ~= "" then
                    tablestr = tablestr..'|-\n'
                    tablestr = tablestr..'| '..labelsList[i]..' || '..assignValueProp(propsList[i], val)..'\n'
                end
            end
            tablestr = tablestr..'|}\n'
        else 
            return "Bad size"
        end
    else
        return '' 
    end

    -- We assign type and category
    tablestr = tablestr.."[[Category:"..template.."s]]"..frame:preprocess("{{#set:Is Type="..template.."}}")

    tablestr = tablestr..printProcesses(frame, template, currentpage)

    return tablestr


end

-- For getting all relevant fields of correspondence
function GetRows(frame, template, field)
    local str = getCorrespondenceRow(frame, template, field)
    return str
end

-- Function for printing processes button
function printProcesses(frame, template, currentpage)

    local template =  mw.text.trim(frame.args[1])

    local processes = getProcessesTemplate(frame, template)

    local str = ""

    for i,v in ipairs(processes) do
        local fields = mw.text.split( processes[i], ',', true )
        str = str.."{{FormLink|"..fields[1].."|"..currentpage.."|Process|Process[Sample]}}"
    end
    return frame:preprocess(str)
end


-- Assign a Value to a property
-- We assume different values separated by commas!
function assignValueProp(prop, val)

    local valuesList = mw.text.split(val, ",") 
    local assignList = {}

    for i,v in ipairs(valuesList) do
        table.insert(assignList, "[["..prop.."::"..v.."|"..v.."]]")
    end

    return table.concat( assignList, ', ')

end


function EntityReadProps(frame, pagetitle, propsList, str)

    for i,v in ipairs(propsList) do
        -- We get value with a parsecall. Later directly from PHP maybe
        if v ~= "" then
            local parsercall = "{{#show:"..pagetitle.."|?"..v.."}}"
            local result = ""
            result = frame:preprocess(parsercall)
            str = str.."*"..v.." : "..result.."*<br>\n"
        end
    end

    return str

end

function EntityWriteProps(frame, pagetitle, propsList, form)
    
    local str = ""
    local form = frame:preprocess("{{#show:"..pagetitle.."|?Is Type#}}")
    -- form is the type of entry
    for i,v in ipairs(propsList) do
        -- We get value with a parsecall. Later directly from PHP maybe
        if v ~= "" then
            local parsercall = "{{#show:"..pagetitle.."|?"..v.."#}}"
            local result = ""
            result = frame:preprocess(parsercall)
            local template = getCorrespondence(frame, 3, v, 1)
            local param = getCorrespondence(frame, 3, v, 2)
            local writecall = "{{EntityFieldChange|"..pagetitle.."|"..result.."|"..template.."|"..param.."|"..form.."}}"
            local writeresult =  frame:preprocess(writecall)
            str = str.."*"..v.." : "..writeresult.."*<br>\n"
         end
    end
    return str
end

function getCorrespondence(frame, query, prop, out)

    local tableprops = {}
    tableprops = getAllCorrespondences( frame )

    for i, v in ipairs( tableprops ) do
        if  tableprops[i][query] == prop then
                return tableprops[i][out]
        end
    end
    return ''
end

-- Get fields relevant to a template
function getCorrespondenceRow(frame, query, field)
    -- All stuff
    local tableprops = {}
    -- Selection
    local listrow = {}

    tableprops = getAllCorrespondences( frame )

    for i, v in ipairs( tableprops ) do
        if  tableprops[i][1] == query then
            table.insert( listrow, tableprops[i][field] )
        end
    end
    
    return table.concat(listrow, ",")

end

-- Get processes correspoing to one template
function getProcessesTemplate(frame, query)

    -- All stuff
    local tableprops = {}
    -- Selection
    local listrow = {}

    tableprops = getAllProcesses( frame )

    for i, v in ipairs( tableprops ) do
        if  tableprops[i][1] == query then
            table.insert( listrow, tableprops[i][2]..","..tableprops[i][3] )
        end
    end
    
    return listrow
end

-- Parsing of Props, Params and Labels list
function getAllCorrespondences( frame ) 

    local str = ""
    local page = "{{#getwikitext:MediaWiki:Props-Correspondences|}}"
    local text =  frame:preprocess(page)
    local lines = mw.text.split( text, '\n', true )
    
    local tableprops = {}

    for i,line in ipairs(lines) do
        -- Ignore starting #
        line = mw.text.trim(line)
        if ( mw.ustring.find( line, "^\s*#") ) then
            -- Do nothing if starting hash comment
        elseif (  mw.ustring.find( line, "^\s*$") ) then
            -- Do nothing if empty string
        else
            tableprops = processTableLineProps(tableprops, line)
        end
    end

    return tableprops

end

-- Parsing of Processes
function getAllProcesses( frame ) 

    local str = ""
    local page = "{{#getwikitext:MediaWiki:Props-Processes|}}"
    local lines = mw.text.split( frame:preprocess(page), '\n', true )
    
    local tableprops = {}

    for i,line in ipairs(lines) do
        -- Ignore starting #
        line = mw.text.trim(line)
        if ( mw.ustring.find( line, "^\s*#") ) then
            -- Do nothing if starting hash comment
        elseif (  mw.ustring.find( line, "^\s*$") ) then
            -- Do nothing if empty string
        else
            tableprops = processTableLineProps(tableprops, line)
        end
    end

    return tableprops

end

-- Simple parsing of rows
function processTableLineProps(tableprops, line)
    local fields = mw.text.split( line, ',', true )
    table.insert( tableprops, fields )

    return tableprops
end

return p

